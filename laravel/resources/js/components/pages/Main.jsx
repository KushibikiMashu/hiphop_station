import React from 'react'
import {BrowserRouter as Router} from 'react-router-dom'
import request from 'superagent'
import {pathToJson} from '../const'
import MainTemplate from '../templates/MainTemplate'
import MainDummyTemplate from '../templates/MainDummyTemplate';
import VideoCardDummy from "../organisms/VideoCardDummy";
import VideoListDummyTemplate from "../templates/VideoListDummyTemplate";

const PATH = pathToJson('main')

export default class Main extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            videos: null
        }
    }

    // コンポーネントがマウントする前に動画情報のJSONを読み込む
    componentWillMount() {
        request.get(PATH)
            .end((err, res) => {
                this.loadedJson(err, res)
            })
    }

    // 読み込んだ全ての動画情報を配列でvideosに格納
    loadedJson(err, res) {
        if (err) {
            console.log('JSON読み込みエラー')
            return
        }
        this.setState({
            videos: res.body
        })
    }

    render() {
        const videos = this.state.videos
        const {classes} = this.props
        const title = 'HIPSTY'

        // asyncでres.bodyがstateに登録されるようにする
        if (!videos) {
            let dummyVideos = []
            for (let i = 0; i < 10; i++) {
                dummyVideos.push(<VideoCardDummy key={i}/>)
            }
            return (
                <Router basename='/'>
                    <MainDummyTemplate title={title} videos={dummyVideos}/>
                </Router>
            )
        }

        return (
            <Router basename='/'>
                <MainTemplate classes={classes} title={title} videos={videos}/>
            </Router>
        )
    }
}
