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
const PATH = "http://localhost:3000/json/songs.json";

const styles = theme => ({
    flex: {
        flexGrow: 1,
    },
    card: {
        // maxWidth: 560,
        // maxHeight: 600,
        justifyContent: 'center',
    },
    media: {
        height: 0,
        paddingTop: '56.25%', // 16:9
    },
    actions: {
        display: 'flex',
    },
    expand: {
        transform: 'rotate(0deg)',
        transition: theme.transitions.create('transform', {
            duration: theme.transitions.duration.shortest,
        }),
        marginLeft: 'auto',
        [theme.breakpoints.up('sm')]: {
            // marginRight: -8,
        },
    },
    expandOpen: {
        transform: 'rotate(180deg)',
    },
    cardContent: {
        paddingTop: 4,
        paddingBottom: 4,
        paddingLeft: 12,
        paddingRight: 12,
    },
    root: {
        justifyContent: 'center'
    }
});

class RecommendVideos extends React.Component {
    constructor(props) {
        super(props);
        // this.state = {
        //     items: null
        // };
    }

    // componentWillMount() {
    //     request.get(PATH)
    //         .end((err, res) => {
    //             this.loadedJson(err, res);
    //         });
    // };

    // loadedJson(err, res) {
    //     if (err) {
    //         console.log('JSON読み込みエラー');
    //         return;
    //     }
    //     console.log(res.body);
    //     this.setState({
    //         items: res.body
    //     });
    //     console.log(this.state.items);
    // };

    render() {
        // asyncでres.bodyがstateに登録されるようにする
        // if (!this.state.items) {
        //     return false;
        // }

        const { classes } = this.props;

        // mapで8個生成する
        const recommendVideo = (
            <Grid container justify='center' direction="row" spacing={16}>
                <Grid item>
                    <Card className={classes.card}>
                        <CardMedia
                            className={classes.media}
                            image="https://i.ytimg.com/vi/JUt2y2TpemY/mqdefault.jpg"
                            component={Link}
                            to={'/video/JUt2y2TpemY'}
                        />
                        {/* <iframe width="312" height="175.5" src="https://www.youtube.com/embed/JUt2y2TpemY" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe> */}
                        {/* <a href={'/react/material/video/' + e.hash} style={{textDecoration : "none"}}> */}
                        <CardContent className={classes.cardContent}>
                            <Typography gutterBottom variant="subheading">
                                {/* {e.title} */}
                                AKLO X ZORN / A to Z TOUR 2018
                            </Typography>
                        </CardContent>
                        {/* </a> */}
                        <CardActions>
                            <Typography variant="caption">
                                {/* {e.channel} */}
                                hotmuzik1989
                            </Typography>
                            <Typography variant="caption">
                                {/* {e.date} */}
                                2018-09-28T03:29:35.000Z
                            </Typography>
                        </CardActions>
                    </Card>
                </Grid>
                <Grid item>
                    <Card className={classes.card}>
                        <CardMedia
                            className={classes.media}
                            image="https://i.ytimg.com/vi/aVjnTPZj5rk/mqdefault.jpg"
                            component={Link}
                            to={'/video/aVjnTPZj5rk'}
                        />
                        {/* <iframe width="312" height="175.5" src="https://www.youtube.com/embed/aVjnTPZj5rk" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe> */}
                        {/* <a href={'/react/material/video/' + e.hash} style={{textDecoration : "none"}}> */}
                        <CardContent className={classes.cardContent}>
                            <Typography gutterBottom variant="subheading">
                                {/* {e.title} */}
                                SIMON JAP - くそったれFor Life Remix
                            </Typography>
                        </CardContent>
                        {/* </a> */}
                        <CardActions>
                            <Typography variant="caption">
                                {/* {e.channel} */}
                                Shinjyuku Tokyo
                            </Typography>
                            <Typography variant="caption">
                                {/* {e.date} */}
                                2018-09-25T16:53:38.000Z
                            </Typography>
                        </CardActions>
                    </Card>
                </Grid>
            </Grid>
        );

        return (
            <React.Fragment>
                <Typography variant="headline" style={{ textAlign: "center", marginTop: 20, marginBottom: 10 }}>
                    あなたへのオススメ
                </Typography>
                <div className={classes.flex}>
                    {recommendVideo}
                    {recommendVideo}
                    {recommendVideo}
                    {recommendVideo}
                </div>
            </React.Fragment>
        );
    }
}

RecommendVideos.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(RecommendVideos);