import React from 'react'
import { Route } from 'react-router-dom'
import PropTypes from 'prop-types'
import { channelId } from '../const'
import VideoList from "./VideoList"
import VideoPlayerTemplate from '../templates/VideoPlayerTemplate'

export default function Routing(props) {
    const {videos} = props
    var main = [],
        MV = [],
        battle = [],
        interview = [],
        otheres = []

    // ジャンルごとに動画を振り分ける。振り分けた動画をVideoListに渡す
    videos.map(video => {
        switch (video.genre) {
            case 'MV':
                MV.push(video)
                break
            case 'battle':
                battle.push(video)
                break
            case 'interview':
                interview.push(video)
                break
            case 'others':
                otheres.push(video)
                break
            default:
                break
        }
        if (video.channelId !== channelId.neetTokyo) {
            main.push(video)
        }
    })

    return (
        <React.Fragment>
            <Route exact path='/' render={() => <VideoList videos={main}/>}/>
            <Route path='/music_video' render={() => <VideoList videos={MV}/>}/>
            <Route path='/battle' render={() => <VideoList videos={battle}/>}/>
            <Route path='/interview' render={() => <VideoList videos={interview}/>}/>
            <Route path='/others' render={() => <VideoList videos={otheres}/>}/>
            <Route path='/video/:hash' render={() => <VideoPlayerTemplate videos={videos}/>}/>
        </React.Fragment>
    )
}

Routing.propTypes = {
    videos: PropTypes.array.isRequired,
}
