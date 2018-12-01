import React from 'react'
import request from 'superagent'
import {pathToJson} from '../const'
import VideoCard from '../organisms/VideoCard'
import VideoCardDummy from '../organisms/VideoCardDummy'
import VideoListTemplate from '../templates/VideoListTemplate'
import VideoListDummyTemplate from '../templates/VideoListDummyTemplate'
import Grid from "@material-ui/core/Grid/Grid";

const PATH = pathToJson("main")

export default class VideoList extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            items: null,
            hasMoreVideos: true,
            loadedVideosCount: 20, // デフォルトの動画表示数
        }
    }

    // コンポーネントがマウントする前に動画情報のJSONを読み込む
    componentWillMount() {
        request.get(PATH)
            .end((err, res) => {
                this.loadedJson(err, res)
            })
    }

    // 読み込んだ全ての動画情報を配列でitemsに格納
    loadedJson(err, res) {
        if (err) {
            return
        }

        this.setState({
            items: res.body
        })
    }

    // 「LOAD MORE」ボタンをクリックすると、新たに10個の動画を表示する
    loadVideos() {
        if (this.state.loadedVideosCount >= this.state.items.length) {
            return
        }

        this.setState({
            loadedVideosCount: this.state.loadedVideosCount + 26
        })
    }

    // loadVideos関数が呼ばれると、再度render関数が作動する
    render() {
        let videos = []

        // asyncでres.bodyがstateに登録されるようにする
        if (!this.state.items) {
            for (let i = 0; i < 10; i++) {
                videos.push(<VideoCardDummy key={i}/>)
            }

            return <VideoListDummyTemplate videos={videos}/>
        }

        // loadedVideosCountの数だけ動画を読み込む
        const items = this.state.items
        for (let i = 0; i < this.state.loadedVideosCount; i++) {
            videos.push(<VideoCard key={i} video={items[i]}/>)
        }

        return (
            <VideoListTemplate videos={videos} onClick={() => {
                this.loadVideos()
            }}/>
        )
    }
}
