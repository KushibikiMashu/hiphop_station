const pathToJson = (filename) => {
    return location.origin + "/json/" + filename + ".json";
}

const newList = location.origin + '/new/list';

export { pathToJson, newList };
