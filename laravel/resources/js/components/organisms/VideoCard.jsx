import React from "react"
import PropTypes from 'prop-types';
import {withStyles} from '@material-ui/core/styles';
import Grid from "@material-ui/core/Grid/Grid"
import Card from "@material-ui/core/Card/Card"
import CustomCardMedia from "../atoms/CustomCardMedia"
import CustomCardContent from "../molecules/CustomCardContent"
import CustomCardActions from "../molecules/CustomCardActions"

const styles = theme => ({
    card: {
        maxWidth: 260,
    },
})

function VideoCard(props) {
    const { classes, video, items, i } = props

    return (
        <Grid item>
            <Card className={classes.card}>
                <CustomCardMedia items={items} i={i}/>
                <CustomCardContent items={items} i={i}/>
                <CustomCardActions channelTitle={video.channel.title} PublishedDate={video.diff_date}/>
            </Card>
        </Grid>
    )
}

VideoCard.propTypes = {
    classes: PropTypes.object.isRequired,
    items: PropTypes.array.isRequired,
    i: PropTypes.number.isRequired,
}

export default withStyles(styles)(VideoCard)
