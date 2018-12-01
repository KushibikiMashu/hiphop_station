import React from "react"
import PropTypes from 'prop-types'
import {withStyles} from '@material-ui/core/styles';
import CardActions from "@material-ui/core/CardActions/CardActions"
import Typography from "@material-ui/core/Typography/Typography"

const styles = theme => ({
    PublishedDate: {
        marginLeft: 'auto'
    }
})

function CustomCardActions(props) {
    const {classes, channelTitle, PublishedDate} = props

    return (
        <CardActions>
            <Typography variant="caption">
                {channelTitle}
            </Typography>
            <Typography variant="caption" className={classes.PublishedDate}>
                {PublishedDate}
            </Typography>
        </CardActions>
    )
}

CustomCardActions.propTypes = {
    classes: PropTypes.object.isRequired,
    channelTitle: PropTypes.string.isRequired,
    PublishedDate: PropTypes.string.isRequired,
}

export default withStyles(styles)(CustomCardActions)
