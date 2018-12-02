import React from 'react';
import { Route } from 'react-router-dom';
import PropTypes from 'prop-types'
import VideoList from "../pages/VideoList";
import VideoPlayerTemplate from '../templates/VideoPlayerTemplate'

export default function Routing(props) {
    const { videos } = props

    // ジャンルごとに動画を振り分ける。振り分けた動画をVideoListに渡す

    return (
        <React.Fragment>
            <Route exact path='/' render={() => <VideoList videos={videos}/>}/>
            <Route path='/music_video' render={() => <VideoList videos={videos} genre='MV'/>}/>
            <Route path='/battle' render={() => <VideoList videos={videos} genre='battle'/>}/>
            <Route path='/interview' render={() => <VideoList videos={videos} genre='interview'/>}/>
            <Route path='/others' render={() => <VideoList videos={videos} genre='others'/>}/>
            <Route path='/video/:hash' render={() => <VideoPlayerTemplate videos={videos}/>}/>
        </React.Fragment>
    );
}

Routing.propTypes = {
    videos: PropTypes.array.isRequired,
    genre: PropTypes.string,
};