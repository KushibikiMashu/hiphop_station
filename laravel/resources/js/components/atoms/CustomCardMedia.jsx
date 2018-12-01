import React from "react"
import {Link} from "react-router-dom"
import PropTypes from 'prop-types';
import {withStyles} from '@material-ui/core/styles';
import CardMedia from "@material-ui/core/CardMedia/CardMedia"

const styles = theme => ({
    media: {
        height: 0,
        paddingTop: '56.25%', // 16:9
        backgroundSize: 'cover',
    },
})

function CustomCardMedia(props) {
    const {classes, items, i} = props

    return (
        <CardMedia
            className={classes.media}
            image={items[i].thumbnail.high}
            component={Link}
            to={"/video/" + items[i].hash}
        />
    )
}

CustomCardMedia.propTypes = {
    classes: PropTypes.object.isRequired,
    items: PropTypes.array.isRequired,
    i: PropTypes.number.isRequired,
}

export default withStyles(styles)(CustomCardMedia)
