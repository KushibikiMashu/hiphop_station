import React from 'react'
import PropTypes from 'prop-types'
import { withStyles } from '@material-ui/core/styles';
import CardActions from '@material-ui/core/CardActions/CardActions'
import Typography from '@material-ui/core/Typography/Typography'
import TwitterIcon from '../atoms/TwitterIcon'

const styles = theme => ({
    root: {
        paddingTop: 0,
        paddingRight: 10,
        paddingLeft: 10,
        display: 'flex',
        alignItems: 'center',
    },
    channelTitle: {
        textAlign: 'left',
    },
    twitter: {
        marginLeft: 'auto',
        paddingRight: 8,
    },
})

function CustomCardActionsPlaying(props) {
    const {classes, video} = props
    return (
        <CardActions className={classes.root}>
            <div>
                <div>
                    <Typography variant='caption' className={classes.channelTitle}>
                        {video.channelTitle}
                    </Typography>
                    <Typography variant='caption'>
                        {video.createdAt}
                    </Typography>
                </div>
            </div>
            <div className={classes.twitter}>
                <TwitterIcon hash={video.hash}/>
            </div>
        </CardActions>
    )
}

CustomCardActionsPlaying.propTypes = {
    classes: PropTypes.object.isRequired,
    video: PropTypes.object.isRequired,
}

export default withStyles(styles)(CustomCardActionsPlaying)
