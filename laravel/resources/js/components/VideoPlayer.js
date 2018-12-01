import React from 'react';
import { Link } from 'react-router-dom';
import request from 'superagent';
import MainVideo from './MainVideo';
import VideoCardPlaying from './organisms/VideoCardPlaying';
import Button from '@material-ui/core/Button';
import { pathToJson } from './const';
const PATH = pathToJson("main");

class VideoPlayer extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            items: null,
        };
    }

    componentWillMount() {
        request.get(PATH)
            .end((err, res) => {
                this.loadedJson(err, res);
            });
    };

    loadedJson(err, res) {
        if (err) {
            return;
        }
        this.setState({
            items: res.body
        });
    };

    render() {
        if (!this.state.items) {
            return false;
        }
        var path = location.pathname;
        var hash = path.split('/').pop();

        var videos = this.props.videos;
        var playingVideo = {};
        videos.map(video => {
            if(video.hash !== hash) {
                return;
            }
            playingVideo = video;
        });

        return (
            <React.Fragment>
                <VideoCardPlaying video={playingVideo} />
                <div style={{'display': 'flex', justifyContent: 'center', marginTop: 12}}>
                    <Button variant="extendedFab" component={Link} to={'/'}>HOME</Button>
                </div>
            </React.Fragment>
        );
    }
}

export default VideoPlayer;
