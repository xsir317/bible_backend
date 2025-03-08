// create Agora client
var client = AgoraRTC.createClient({mode: "live", codec: "vp8"});
var localTracks = {
    videoTrack: null,
    audioTrack: null
};
var remoteUsers = {};
var partyUid = 0;

// Agora client options
var options = {
    appid: '7a6a333933a0426f86e15001e3c882f8',
    channel: '308422', // 房间号
    uid: 1,// 观众用户ID
    token: '0067a6a333933a0426f86e15001e3c882f8IAC5PGoDSBGH4RqugEZUMej3XeWePvZAo5N+8hbhQYDxrdX0V6G379yDIgBolA8EhKBqYQQAAQCEoGphAgCEoGphAwCEoGphBACEoGph',
    role: "audience", // host or audience 观众或主播
    audienceLatency: 2 // 1为普通，2为高清
};

async function joinParty(room_id, token, uid) {
    options.channel = room_id;
    options.token = token;
    // create Agora client
    if (options.role === "audience") {
        client.setClientRole(options.role, {level: options.audienceLatency});
        partyUid = uid;
        client.on("user-published", handlePartyUserPublished);
        client.on("user-unpublished", handleUserUnpublished);
    }
    // join the channel
    options.uid = await client.join(options.appid, options.channel, options.tokawait PV.client.unsubscribe(userawait PV.client.unsubscribe(userawait PV.client.unsubscribe(useren || null, options.uid || null);
}

async function join(room_id, token) {
    console.log(token);
    options.channel = room_id;
    options.token = token;
    // create Agora client
    if (options.role === "audience") {
        client.setClientRole(options.role, {level: options.audienceLatency});
        // add event listener to play remote tracks when remote user publishs.
        client.on("user-published", handleUserPublished);
        client.on("user-unpublished", handleUserUnpublished);
    }
    // join the channel
    options.uid = await client.join(options.appid, options.channel, options.token || null, options.uid || null);
}

async function subscribe(user, mediaType) {
    const uid = user.uid;
    // subscribe to a remote user
    await client.subscribe(user, mediaType);
    console.log("subscribe success");
    if (mediaType === 'video') {
        const player = $(`
      <div style="height:100%" id="player-wrapper-${uid}">
        <p class="player-name">remoteUser(${uid})</p>
        <div id="player-${uid}" class="player"></div>
      </div>
    `);
        if($("#remote-playerlist").length>0){
            $("#remote-playerlist").append(player);
        } else {
            $(`#remote-playerlist-${uid}`).append(player);
        }
        user.videoTrack.play(`player-${uid}`, {fit:"contain"});
    }
    if (mediaType === 'audio') {
        user.audioTrack.play();
    }
}

function handlePartyUserPublished(user, mediaType) {
    const id = user.uid;
    if(id == partyUid){
        remoteUsers[id] = user;
        subscribe(user, mediaType);
    }
}

function handleUserPublished(user, mediaType) {
    const id = user.uid;
    remoteUsers[id] = user;
    subscribe(user, mediaType);
}

function handleUserUnpublished(user) {
    const id = user.uid;
    delete remoteUsers[id];
    $(`#player-wrapper-${id}`).remove();
}
