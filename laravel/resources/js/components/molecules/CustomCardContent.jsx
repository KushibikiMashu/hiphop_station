import React from "react"
import PropTypes from 'prop-types';
import {withStyles} from '@material-ui/core/styles';
import CardContent from "@material-ui/core/CardContent/CardContent"
import Typography from "@material-ui/core/Typography/Typography"

const styles = theme => ({
    cardContent: {
        paddingTop: 8,
        paddingBottom: 4,
        paddingLeft: 12,
        paddingRight: 12,
    },
})

function CustomCardContent(props) {
    const {classes, items, i} = props

    return (
        <CardContent className={classes.cardContent}>
            <Typography gutterBottom variant="subheading">
                {items[i].title}
            </Typography>
        </CardContent>
    )
}

CustomCardContent.propTypes = {
    classes: PropTypes.object.isRequired,
    items: PropTypes.array.isRequired,
}

export default withStyles(styles)(CustomCardContent)
