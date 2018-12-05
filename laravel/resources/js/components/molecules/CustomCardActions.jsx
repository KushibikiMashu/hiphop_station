import React from 'react'
import PropTypes from 'prop-types'
import { withStyles } from '@material-ui/core/styles';
import CardActions from '@material-ui/core/CardActions/CardActions'
import Typography from '@material-ui/core/Typography/Typography'
import TwitterIcon from '../atoms/TwitterIcon'

const styles = theme => ({
    root: {
        paddingTop: 0,
        display: 'flex',
        alignItems: 'center',
    },
    content: {
        paddingLeft: 6,
    },
    twitter: {
        marginLeft: 'auto',
        paddingRight: 8,
    },
})

function CustomCardActions(props) {
    const {classes, video} = props
    return (
        <CardActions className={classes.root}>
            <div className={classes.content}>
                <div>
                    <Typography variant='caption'>
                        {video.channelTitle}
                    </Typography>
                    <Typography variant='caption'>
                        {video.date}
                    </Typography>
                </div>
            </div>
            <div className={classes.twitter}>
                <TwitterIcon hash={video.hash}/>
            </div>
        </CardActions>
    )
}

CustomCardActions.propTypes = {
    classes: PropTypes.object.isRequired,
    video: PropTypes.object.isRequired,
}

export default withStyles(styles)(CustomCardActions)
