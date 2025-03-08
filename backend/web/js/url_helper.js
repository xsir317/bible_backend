// URL 处理类
var jsUrlHelper = {
    getUrlParam: function (url, ref) {
        var str = "";
        // 如果不包括此参数
        if (url.indexOf(ref) == -1)
            return "";
        str = url.substr(url.indexOf('?') + 1);
        var arr = str.split('&');
        for (var i in arr) {
            var paired = arr[i].split('=');
            if (paired[0] == ref) {
                return paired[1];
            }
        }
        return "";
    },
    putUrlParam: function (url, ref, value) {
        // 如果没有参数
        if (url.indexOf('?') == -1)
            return url + "?" + ref + "=" + value;

        // 如果不包括此参数
        if (url.indexOf(ref) == -1)
            return url + "&" + ref + "=" + value;
        var arr_url = url.split('?');
        var base = arr_url[0];
        var arr_param = arr_url[1].split('&');
        for (var i = 0; i < arr_param.length; i++) {
            var paired = arr_param[i].split('=');
            if (paired[0] == ref) {
                paired[1] = value;
                arr_param[i] = paired.join('=');
                break;
            }
        }
        return base + "?" + arr_param.join('&');
    },
    delUrlParam: function (url, ref) {
        // 如果不包括此参数
        if (url.indexOf(ref) == -1)
            return url;
        var arr_url = url.split('?');
        var base = arr_url[0];
        var arr_param = arr_url[1].split('&');
        var _arr_param = [];
        for (var i = 0; i < arr_param.length; i++) {
            var paired = arr_param[i].split('=');
            if (paired[0] !== ref) {
                _arr_param.push(arr_param[i]);
            }
        }
        var _url = '';
        if (_arr_param.length > 0) {
            _url = "?" + _arr_param.join('&');
        }
        return base + _url;
    }
};