const pathToJson = (filename) => {
    return location.origin + "/json/" + filename + ".json"
}

const newList = location.origin + "/api/video/new"
const videoList = location.origin + "/api/video/list"

const channelId = {"neetTokyo": 63}

const CONST = {
    "baseUrl": location.origin,
    "videoUrl": location.origin + "/video",
    "newList": location.origin + "/api/video/new",
    "videoList": location.origin + "/api/video/list",
    "youtubeEnbedUrl": "https://www.youtube.com/embed/",

    "siteTitle": "HIPSTY | 日本語ラップ好きのための動画サイト",
    "channelId": {"neetTokyo": 63},
}

export { pathToJson, newList, videoList, channelId, CONST }
