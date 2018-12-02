import React from 'react'
import PropTypes from 'prop-types'
import {withStyles} from '@material-ui/core/styles'
import Card from '@material-ui/core/Card'
import ResponsiveIframe from '../atoms/ResponsiveIframe'
import CustomCardContent from "../molecules/CustomCardContent";
import CustomCardActions from '../molecules/CustomCardActions'

const styles = theme => ({
    root: {
        display: 'block',
        textAlign: 'center'
    },
    card: {
        width: '100%',
        justifyContent: 'center',
    },
})

function VideoCardPlaying(props) {
    const {classes, video} = props
    const src = 'https://www.youtube.com/embed/' + video.hash

    return (
        <div className={classes.root}>
            <Card className={classes.card}>
                <ResponsiveIframe src={src}/>
                <CustomCardContent title={video.title}/>
                <CustomCardActions title={video.channel.title} date={video.published_at}/>
            </Card>
        </div>
    )
}

VideoCardPlaying.propTypes = {
    classes: PropTypes.object.isRequired,
    video: PropTypes.array.isRequired,
}

export default withStyles(styles)(VideoCardPlaying)