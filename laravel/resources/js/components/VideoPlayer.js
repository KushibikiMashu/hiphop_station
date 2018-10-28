import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import classnames from 'classnames';
import Card from '@material-ui/core/Card';
import CardMedia from '@material-ui/core/CardMedia';
import CardContent from '@material-ui/core/CardContent';
import CardActions from '@material-ui/core/CardActions';
import Typography from '@material-ui/core/Typography';
import red from '@material-ui/core/colors/red';
import Grid from '@material-ui/core/Grid';
import Hidden from '@material-ui/core/Hidden';

import NavigationIcon from '@material-ui/icons/Navigation';
import { Link } from 'react-router-dom';

import request from 'superagent';
import MainVideo from './MainVideo';
import RecommendVideos from './RecommendVideos';
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
            console.log('JSON読み込みエラー');
            return;
        }
        // console.log(res.body);
        this.setState({
            items: res.body
        });
        // console.log(this.state.items);
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
                <MainVideo hash={hash} video={playingVideo} />
                <div style={{'display': 'flex', justifyContent: 'center', marginTop: 12}}>
                    <Button
                    variant="extendedFab" 
                    component={Link}
                    to={'/'}
                    >
                        HOME
                    </Button>
                </div>
                {/* <RecommendVideos /> */}
            </React.Fragment>
        );
    }
}

// VideoPlayer.propTypes = {
//     classes: PropTypes.object.isRequired,
// };

export default VideoPlayer;
