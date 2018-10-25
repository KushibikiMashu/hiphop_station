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
const PATH = "http://ec2-54-163-220-138.compute-1.amazonaws.com/json/songs.json";

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

class MainVideo extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const { classes, hash } = this.props;
        var video = this.props.video;
        var src = "https://www.youtube.com/embed/" + video.hash;

        return (
            <React.Fragment>
                <Grid container justify='center' spacing={16}>
                    <Grid item>
                        <div style={{ display: "block", textAlign: "center" }}>
                            <Card className={classes.card}>
                                {/* <CardMedia className={classes.media}> */}
                                <iframe width="640" height="360" src={src} frameBorder="0" allow="autoplay; encrypted-media" allowFullScreen></iframe>
                                {/* </CardMedia> */}
                                <CardContent className={classes.cardContent}>
                                    <Typography gutterBottom variant="subheading">
                                        {video.title}
                                    </Typography>
                                </CardContent>
                                <CardActions>
                                    <Typography variant="caption">
                                        {video.channel.title}
                                    </Typography>
                                    <Typography variant="caption">
                                        {video.published_at}
                                    </Typography>
                                </CardActions>
                            </Card>
                        </div>
                    </Grid>
                </Grid>
            </React.Fragment>
        );
    }
}

MainVideo.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(MainVideo);