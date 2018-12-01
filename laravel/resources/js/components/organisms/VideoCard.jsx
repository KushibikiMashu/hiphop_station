import React from "react"
import Grid from "@material-ui/core/Grid/Grid"
import Card from "@material-ui/core/Card/Card"
import {withStyles} from "@material-ui/core"
import PropTypes from 'prop-types';
import CustomCardMedia from "../atoms/CustomCardMedia"
import CustomCardContent from "../molecules/CustomCardContent"
import CustomCardActions from "../molecules/CustomCardActions"

const styles = theme => ({
    card: {
        maxWidth: 260,
    },
})

function VideoCard(props) {
    const {classes, items, i} = props

    return (
        <Grid item>
            <Card className={classes.card}>
                <CustomCardMedia items={items} i={i}/>
                <CustomCardContent items={items} i={i}/>
                <CustomCardActions items={items} i={i}/>
            </Card>
        </Grid>
    )
}

VideoCard.propTypes = {
    classes: PropTypes.object.isRequired,
    items: PropTypes.object.isRequired,
    i: PropTypes.number.isRequired,
}

export default withStyles(styles)(VideoCard)
