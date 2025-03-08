var LMqtt = (function () {
    // 构造函数
    function LMqtt(opt) {
        console.log('init....');
        if (opt.host) {
            host = opt.host;
        }
        if (opt.port) {
            port = opt.port;
        }
        if (opt.username) {
            username = opt.username;
        }
        if (opt.password) {
            password = opt.password;
        }
        if (opt.client_id) {
            client_id = opt.client_id;
        }
    }

    var host = '118.178.213.19',
        port = 8083,
        username,
        password,
        use_ssl = false,
        timeout = 3,
        client_id,
        mqtt2,
        debug = false,
        acitve = false,
        receiveFunc,
        topicData = [];

    function Debug(something) {
        if (debug) {
            console.log(something);
        }
    }

    // 登录
    function _connect() {
        mqtt2 = new Paho.MQTT.Client(
            host, //MQTT域名
            port, //WebSocket端口，如果使用HTTPS加密则配置为443,否则配置80
            client_id //客户端ClientId
        );
        // 断开事件
        mqtt2.onConnectionLost = _connectLost;
        // 收到消息
        mqtt2.onMessageArrived = _receiveMsg;

        var options = {
            timeout: timeout,
            useSSL: use_ssl,
            onSuccess: _connectSuccess,
            onFailure: _connectFailed,
        };
        Debug("username=" + username);
        if (username) {
            options.userName = username;
            options.password = password;
        }
        mqtt2.connect(options);
    }

    // 断开连接操作
    function _connectLost(response) {
        acitve = false;
        Debug("连接断开,正在重连...");
        _connect();
    }

    // 收到消息处理
    function _receiveMsg(response) {
        Debug("收到消息");
        var topic = response.destinationName;
        var payload = response.payloadString;

        // 如果有自定义方法
        if (receiveFunc) {
            receiveFunc(topic, payload);
            return true;
        }
        console.log("Topic:" + topic);
        console.log("Payload:" + payload);
    }

    // 登录成功
    function _connectSuccess(response) {
        acitve = true;
        Debug("登录成功");
        _replySubscribe();
    }

    // 登录失败
    function _connectFailed(response) {
        acitve = false;
        Debug("连接断开,正在重连...");
        _connect();
    }

    // 断线后重新订阅 topic
    function _replySubscribe() {
        // 重新订阅之前的数据
        if (topicData.length > 0) {
            for (var i = 0; i < topicData.length; i++) {
                Debug("订阅消息Topic:" + topicData[i]);
                mqtt2.subscribe(topicData[i]);
            }
        }
    }

    // MQTT 初始化登录
    LMqtt.prototype.init = function () {
        _connect();
    };
    // 订阅topic
    LMqtt.prototype.subscribe = function (topic) {
        // 写入对象池
        topicData.push(topic);
        // 在线直接订阅
        if (acitve) {
            Debug("订阅消息Topic:" + topic);
            mqtt.subscribe(topic);
        }
    };
    // 设置回调方法
    LMqtt.prototype.setReceiveFunc = function (func) {
        receiveFunc = func;
    };
    LMqtt.prototype.setDebug = function (flag) {
        debug = flag;
    };
    return LMqtt;
}());