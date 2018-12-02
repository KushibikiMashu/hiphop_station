import React from 'react'
import request from 'superagent'
import { videoList } from '../const'
import MainTemplate from '../templates/MainTemplate'
import MainDummyTemplate from '../templates/MainDummyTemplate';
import VideoCardDummy from "../organisms/VideoCardDummy";

const PATH = videoList

export default class Main extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            videos: null
        }
    }

    // コンポーネントがマウントする前に動画情報のJSONを読み込む
    componentWillMount() {
        if (this.state.videos === null) {
            request.get(PATH)
                .end((err, res) => {
                    this.loadedJson(err, res)
                })
        }
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
        const title = 'HIPSTY'

        // state.videosがnullの間はDummyを表示する
        if (!videos) {
            let dummyVideos = []
            for (let i = 0; i < 10; i++) {
                dummyVideos.push(<VideoCardDummy key={i}/>)
            }
            return (
                <MainDummyTemplate title={title} videos={dummyVideos}/>
            )
        }

        return (
            <MainTemplate title={title} videos={videos}/>
        )
    }
}
