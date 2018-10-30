import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Card from '@material-ui/core/Card';
import CardMedia from '@material-ui/core/CardMedia';
import CardContent from '@material-ui/core/CardContent';
import CardActions from '@material-ui/core/CardActions';
import Typography from '@material-ui/core/Typography';
import Grid from '@material-ui/core/Grid';
import { Link } from 'react-router-dom';

const styles = theme => ({
    flex: {
        flexGrow: 1,
    },
    card: {
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
    }

    render() {
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
                        <CardContent className={classes.cardContent}>
                            <Typography gutterBottom variant="subheading">
                                AKLO X ZORN / A to Z TOUR 2018
                            </Typography>
                        </CardContent>
                        <CardActions>
                            <Typography variant="caption">
                                hotmuzik1989
                            </Typography>
                            <Typography variant="caption">
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
                        <CardContent className={classes.cardContent}>
                            <Typography gutterBottom variant="subheading">
                                SIMON JAP - くそったれFor Life Remix
                            </Typography>
                        </CardContent>
                        <CardActions>
                            <Typography variant="caption">
                                Shinjyuku Tokyo
                            </Typography>
                            <Typography variant="caption">
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
