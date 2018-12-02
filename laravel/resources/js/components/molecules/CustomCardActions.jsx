import React from "react"
import PropTypes from 'prop-types'
import { withStyles } from '@material-ui/core/styles';
import CardActions from "@material-ui/core/CardActions/CardActions"
import Typography from "@material-ui/core/Typography/Typography"

const styles = theme => ({
    root: {
        paddingLeft: 12,
        paddingRight: 12,
    },
    date: {
        marginLeft: 'auto',
    }
})

function CustomCardActions(props) {
    const {classes, title, date} = props

    return (
        <CardActions className={classes.root}>
            <Typography variant="caption">
                {title}
            </Typography>
            <Typography variant="caption" className={classes.date}>
                {date}
            </Typography>
        </CardActions>
    )
}

CustomCardActions.propTypes = {
    classes: PropTypes.object.isRequired,
    title: PropTypes.string.isRequired,
    date: PropTypes.string.isRequired,
}

export default withStyles(styles)(CustomCardActions)
