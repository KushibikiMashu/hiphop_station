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
    const {classes, hash, thumbnail} = props
    const videoPlayer = '/video/' + hash

    return (
        <CardMedia
            className={classes.media}
            image={thumbnail}
            component={Link}
            to={videoPlayer}
        />
    )
}

CustomCardMedia.propTypes = {
    classes: PropTypes.object.isRequired,
    hash: PropTypes.string.isRequired,
    thumbnail: PropTypes.string.isRequired,
}

export default withStyles(styles)(CustomCardMedia)
