import React from 'react'
import {Link} from 'react-router-dom'
import {withStyles} from "@material-ui/core";
import MainVideo from './MainVideo'
import Button from '@material-ui/core/Button'

const styles = theme => ({
    button: {
        'display': 'flex',
        justifyContent: 'center',
        marginTop: 12,
    }
})

function VideoPlayerTemplate() {
    // propsでvideoのオブジェクトを渡してもらう。
    // そのvideoをMainVideoに渡す。
    const {classes, videos} = props

    var hash = location.pathname.split('/').pop()

    var playingVideo = {}
    videos.map(video => {
        if (video.hash !== hash) {
            return
        }
        playingVideo = video
    })

    return (
        <React.Fragment>
            <MainVideo hash={hash} video={playingVideo}/>
            <div className={classes.button}>
                <Button variant="extendedFab" component={Link} to={'/'}>HOME</Button>
            </div>
        </React.Fragment>
    )
}

VideoPlayerTemplate.propTypes = {
    classes: PropTypes.object.isRequired,
}

export default withStyles(styles)(VideoPlayerTemplate)

