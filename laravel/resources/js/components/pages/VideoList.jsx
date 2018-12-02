import React from 'react'
import request from 'superagent'
import {pathToJson} from '../const'
import VideoCard from '../organisms/VideoCard'
import VideoCardDummy from '../organisms/VideoCardDummy'
import VideoListTemplate from '../templates/VideoListTemplate'
import VideoListDummyTemplate from '../templates/VideoListDummyTemplate'

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

    // 「LOAD MORE」ボタンをクリックすると、新たに10個の動画を表示する
    loadVideos() {
        // if (this.state.loadedVideosCount >= this.state.items.length) {
        //     return
        // }

        this.setState({
            loadedVideosCount: this.state.loadedVideosCount + 26
        })
    }

    // loadVideos関数が呼ばれると、再度render関数が作動する
    render() {
        const {videos, genre} = this.props
        let items = []
        if(!genre){
            for (let i = 0; i < this.state.loadedVideosCount; i++) {
                // 親コンポーネントから指定されたジャンルの動画のみ追加する
                items.push(<VideoCard key={i} video={videos[i]}/>)
            }
        } else {
            let i = 0
            console.log('通ってる');
            while(items.length < this.state.loadedVideosCount){
                // 親コンポーネントから指定されたジャンルの動画のみ追加する
                if (videos[i].genre !== genre || videos[i] === undefined) {
                    i++
                    console.log(i);
                    continue;
                }
                items.push(<VideoCard key={i} video={videos[i]}/>)
                i++
                console.log(i);
                if(videos.length === i){
                    break
                }
            }
        }

        return (
            <VideoListTemplate videos={items} onClick={() => {
                this.loadVideos()
            }}/>
        )
    }
}
