import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Card from '@material-ui/core/Card';
import CardContent from '@material-ui/core/CardContent';
import CardActions from '@material-ui/core/CardActions';
import Typography from '@material-ui/core/Typography';
import Grid from '@material-ui/core/Grid';
import ResponsiveIframe from './ResponsiveIframe';
import CustomCardActions from './molecules/CustomCardActions';

const styles = theme => ({
    flex: {
        flexGrow: 1,
    },
    card: {
        width: '100%',
        // maxHeight: 600,
        justifyContent: 'center',
    },
    cardContent: {
        paddingTop: 4,
        paddingBottom: 4,
        paddingLeft: 12,
        paddingRight: 12,
    },
});

class MainVideo extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        const { classes, video } = this.props;
        // var video = this.props.video;
        var src = "https://www.youtube.com/embed/" + video.hash;
        console.log(video.published_at);

        return (
            <React.Fragment>
                <Grid container justify='center' spacing={16}>
                    <Grid item>
                        <div style={{ display: "block", textAlign: "center" }}>
                            <Card className={classes.card}>
                                <ResponsiveIframe src={src} />
                                <CardContent className={classes.cardContent}>
                                    <Typography gutterBottom variant="subheading">
                                        {video.title}
                                    </Typography>
                                </CardContent>
                                {/*<CustomCardActions channelTitle={video.channel.title} PublishedDate={video.published_at}/>*/}
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
