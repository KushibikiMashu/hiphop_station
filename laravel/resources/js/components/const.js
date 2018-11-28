const pathToJson = (filename) => {
    return location.origin + "/json/" + filename + ".json";
}

const newList = location.origin + '/api/video/new';

export { pathToJson, newList };
