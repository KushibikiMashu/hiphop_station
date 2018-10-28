const pathToJson = (filename) => {
    return location.origin + "/json/" + filename + ".json";
}

export { pathToJson };
