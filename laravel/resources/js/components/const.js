const pathToJson = (filename) => {
    return location.origin + "/json/" + filename + ".json";
}

const newList = location.origin + '/api/video/new';
const videoList = location.origin + '/api/video/list';

export { pathToJson, newList, videoList };
