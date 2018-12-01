import React from "react"
import {withStyles} from "@material-ui/core"
import PropTypes from 'prop-types'
import CardActions from "@material-ui/core/CardActions/CardActions"
import Typography from "@material-ui/core/Typography/Typography"

const styles = theme => ({
    diffDate: {
        marginLeft: 'auto'
    }
})

function CustomCardActions(props) {
    const {classes, items, i} = props

    return (
        <CardActions>
            <Typography variant="caption">
                {items[i].channel.title}
            </Typography>
            <Typography variant="caption" className={classes.diffDate}>
                {items[i].diff_date}
            </Typography>
        </CardActions>
    )
}

CustomCardActions.propTypes = {
    classes: PropTypes.object.isRequired,
    items: PropTypes.object.isRequired,
    i: PropTypes.number.isRequired,
}

export default withStyles(styles)(CustomCardActions)
