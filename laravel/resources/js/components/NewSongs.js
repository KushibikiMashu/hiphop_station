import React, {Component} from 'react';
import PropTypes from 'prop-types';
import {withStyles} from '@material-ui/core/styles';
import Card from '@material-ui/core/Card';
import CardMedia from '@material-ui/core/CardMedia';
import CardContent from '@material-ui/core/CardContent';
import CardActions from '@material-ui/core/CardActions';
import Typography from '@material-ui/core/Typography';
import Grid from '@material-ui/core/Grid';
import Button from '@material-ui/core/Button';
import {Link} from 'react-router-dom';
import request from 'superagent';
import {pathToJson} from './const';
import LinearProgress from '@material-ui/core/LinearProgress';
import VideoCard from './organisms/VideoCard';
import LabelBottomNavigation from "./LabelBottomNavigation";

const PATH = pathToJson("main");

const styles = theme => ({
    flex: {
        flexGrow: 1,
    },
    card: {
        maxWidth: 260,
    },
    media: {
        height: 0,
        paddingTop: '56.25%', // 16:9
    },
    headline: {
        textAlign: 'center',
        marginTop: -8,
        paddingBottom: 12,
    },
    cardContent: {
        paddingTop: 8,
        paddingBottom: 4,
        paddingLeft: 12,
        paddingRight: 12,
    },
    button: {
        marginTop: 12,
        marginBottom: 44,
    },
    flexDummy: {
        maxHeight: 640
    },
    cardDummy: {
        maxWidth: 260,
    },
    cardContentDummy: {
        height: 80,
        paddingTop: 8,
        paddingBottom: 4,
        paddingLeft: 12,
        paddingRight: 12,
    },
    mediaDummy: {
        height: 0,
        width: 260,
        backgroundColor: '#E0E0E0',
        paddingTop: '56.25%', // 16:9
    },
    progressLong: {
        height: 16,
        width: 140,
        marginLeft: 8,
        marginTop: 12,
        borderRadius: 20,
        backgroundColor: '#BDBDBD',
    },
    progressShort: {
        height: 16,
        width: 100,
        marginLeft: 8,
        marginTop: 12,
        borderRadius: 20,
        backgroundColor: '#BDBDBD',
    },
    diffDate: {
        marginLeft: 'auto'
    }
});

class VideoList extends Component {
    render() {
        return <div className={this.props.classes.flex}>
            <Grid container justify='center' direction="row" spacing={16}>
                {this.props.videos}
            </Grid>
            <Grid container justify='center' direction="row">
                <Button variant="extendedFab" aria-label="Load" className={this.props.classes.button}
                        onClick={this.props.onClick}>
                    LOAD MORE
                </Button>
            </Grid>
        </div>;
    }
}

VideoList.propTypes = {
    classes: PropTypes.any,
    videos: PropTypes.arrayOf(PropTypes.any),
    onClick: PropTypes.func
};

function DummyVideoCard(props) {
    return <Grid item>
        <Card className={props.classes.cardDummy}>
            <CardMedia
                className={props.classes.mediaDummy} // 黒にする
            />
            <CardContent className={props.classes.cardContentDummy}>
                <LinearProgress className={props.classes.progressLong}/>
                <LinearProgress className={props.classes.progressShort}/>
            </CardContent>
            <CardActions>
            </CardActions>
        </Card>
    </Grid>;
}

DummyVideoCard.propTypes = {classes: PropTypes.any};

function DummyVideoList(props) {
    return <div className={props.classes.flexDummy}>
        <Grid container justify='center' direction="row" spacing={16}>
            {props.videos}
        </Grid>
    </div>;
}

DummyVideoList.propTypes = {
    classes: PropTypes.any,
    videos: PropTypes.arrayOf(PropTypes.any)
};

class NewSongs extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            items: null,
            hasMoreVideos: true,
            loadedVideosCount: 20, // デフォルトの動画表示数
        };
    }

    // コンポーネントがマウントする前に動画情報のJSONを読み込む
    componentWillMount() {
        request.get(PATH)
            .end((err, res) => {
                this.loadedJson(err, res);
            });
    };

    // 読み込んだ全ての動画情報を配列でitemsに格納
    loadedJson(err, res) {
        if (err) {
            return;
        }

        this.setState({
            items: res.body
        });
    };

    // 「LOAD MORE」ボタンをクリックすると、新たに10個の動画を表示する
    loadVideos() {
        if (this.state.loadedVideosCount >= this.state.items.length) {
            return;
        }

        this.setState({
            loadedVideosCount: this.state.loadedVideosCount + 25
        });
    }

    // loadVideos関数が呼ばれると、再度render関数が作動する
    render() {
        const {classes} = this.props;
        var videos = [];

        // asyncでres.bodyがstateに登録されるようにする
        if (!this.state.items) {

            for (var i = 0; i < 10; i++) {
                videos.push(
                    <DummyVideoCard key={i} classes={classes}/>
                )
            }

            return (
                <DummyVideoList classes={classes} videos={videos}/>
            );
        }

        // loadedVideosCountの数だけ動画を読み込む
        const items = this.state.items;
        for (var i = 0; i < this.state.loadedVideosCount; i++) {
            videos.push(
                <VideoCard key={i} items={items} i={i}/>
            )
        }

        return (
            <VideoList classes={classes} videos={videos} onClick={() => {
                this.loadVideos()
            }}/>
        );
    }
}

NewSongs.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(NewSongs);
