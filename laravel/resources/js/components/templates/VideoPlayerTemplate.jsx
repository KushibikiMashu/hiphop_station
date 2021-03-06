import React from 'react'
import PropTypes from 'prop-types'
import { Link } from 'react-router-dom'
import { withStyles } from "@material-ui/core"
import VideoCardPlaying from '../organisms/VideoCardPlaying'
import Grid from "@material-ui/core/Grid/Grid"
import Button from '@material-ui/core/Button'

const styles = theme => ({
    button: {
        marginTop: 12,
        marginBottom: 44,
    }
})

function VideoPlayerTemplate(props) {
    // propsでvideoのオブジェクトを渡してもらう。
    // そのvideoをMainVideoに渡す。
    const {classes, videos} = props
    var hash = location.pathname.split('/').pop()
    var playingVideo = []
    videos.map(video => {
        if (video.hash !== hash) {
            return
        }
        playingVideo = video
    })
    return (
        <React.Fragment>
            <Grid container justify='center' spacing={16}>
                <Grid item>
                    <VideoCardPlaying video={playingVideo}/>
                </Grid>
            </Grid>
            <Grid container justify='center' direction="row">
                <Button variant="extendedFab" className={classes.button} component={Link} to={'/'}>HOME</Button>
            </Grid>
        </React.Fragment>
    )
}

VideoPlayerTemplate.propTypes = {
    classes: PropTypes.object.isRequired,
    videos: PropTypes.array.isRequired,
}

export default withStyles(styles)(VideoPlayerTemplate)

