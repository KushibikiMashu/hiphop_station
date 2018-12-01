import React from "react"
import CardContent from "@material-ui/core/CardContent/CardContent"
import Typography from "@material-ui/core/Typography/Typography"
import {withStyles} from "@material-ui/core"

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
    items: PropTypes.object.isRequired,
}

export default withStyles(styles)(CustomCardContent)
