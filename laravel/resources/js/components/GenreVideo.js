import React from 'react';
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
import {pathToJson, newList} from './const';
import LinearProgress from '@material-ui/core/LinearProgress';

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
    labelTopNavigation: {
        bottom: 0,
        position: 'fixed',
    }
});

class GenreVideo extends React.Component {
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
        request.get(newList)
            .end((err, res) => {
                this.setItems(err, res);
                // this.loadedJson(err, res);
            });
    };

    // 読み込んだ全ての動画情報を配列でitemsに格納
    setItems(err, res) {
        if (err) {
            return;
        }

        this.setState({
            // hasMoreVideos: res.body.length < 20 ? false : true,
            items: res.body
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
        const {classes, genre} = this.props;
        var videos = [];

        // asyncでres.bodyがstateに登録されるようにする
        if (!this.state.items) {

            for (var i = 0; i < 10; i++) {
                videos.push(
                    <Grid item key={i}>
                        <Card className={classes.cardDummy}>
                            <CardMedia
                                className={classes.mediaDummy} // 黒にする
                            />
                            <CardContent className={classes.cardContentDummy}>
                                <LinearProgress className={classes.progressLong}/>
                                <LinearProgress className={classes.progressShort}/>
                            </CardContent>
                            <CardActions>
                            </CardActions>
                        </Card>
                    </Grid>
                )
            }

            return (
                <div className={classes.flexDummy}>
                    <Grid container justify='center' direction="row" spacing={16}>
                        {videos}
                    </Grid>
                </div>
            );
        }
        
        // loadedVideosCountの数だけ動画を読み込む
        const items = this.state.items;
        for (var i = 0; i < this.state.loadedVideosCount; i++) {
            // battleのみを表示する
            if(items[i].genre !== genre || items[i] === undefined){
                continue;
            }
            videos.push(
                <Grid item key={i}>
                    <Card className={classes.card}>
                        <CardMedia
                            className={classes.media}
                            image={items[i].thumbnail}
                            component={Link}
                            to={'/video/' + items[i].hash}
                        />
                        <CardContent className={classes.cardContent}>
                            <Typography gutterBottom variant="subheading">
                                {items[i].title}
                            </Typography>
                        </CardContent>
                        <CardActions>
                            <Typography variant="caption">
                                {items[i].channel_title}
                            </Typography>
                            <Typography variant="caption">
                                {items[i].published_at}
                            </Typography>
                        </CardActions>
                    </Card>
                </Grid>
            )
        }

        return (
            <div className={classes.flex}>
                <Grid container justify='center' direction="row" spacing={16}>
                    {videos}
                </Grid>
                <Grid container justify='center' direction="row">
                    <Button variant="extendedFab" aria-label="Load" className={classes.button} onClick={() => {
                        this.loadVideos()
                    }}>
                        LOAD MORE
                    </Button>
                </Grid>
            </div>
        );
    }
}

GenreVideo.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(GenreVideo);
