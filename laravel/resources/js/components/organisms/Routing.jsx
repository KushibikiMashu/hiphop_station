import React from 'react';
import {Route} from 'react-router-dom';
import VideoList from "../pages/VideoList";
import VideoPlayer from '../VideoPlayer'
import GenreVideo from "../GenreVideo";

export default function Routing(props) {
    const {videos} = props

    return (
        <React.Fragment>
            <Route exact path='/' component={VideoList}/>
            <Route path='/video/:hash' render={() => <VideoPlayer videos={videos}/>}/>
            <Route path='/music_video' render={() => <GenreVideo genre='MV'/>}/>
            <Route path='/battle' render={() => <GenreVideo genre='battle'/>}/>
            <Route path='/interview' render={() => <GenreVideo genre='interview'/>}/>
            <Route path='/others' render={() => <GenreVideo genre='others'/>}/>
        </React.Fragment>
    );
}

Routing.propTypes = {
    // videos: PropTypes.array.isRequired,
    // genre: PropTypes.string.isRequired,
};
