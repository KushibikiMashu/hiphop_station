import React from "react"
import PropTypes from 'prop-types'
import { withStyles } from '@material-ui/core/styles'
import Grid from "@material-ui/core/Grid/Grid"

const styles = theme => ({
    flex: {
        flexGrow: 1,
    },
})

function VideoListDummyTemplate(props) {
    const {classes, videos} = props
    return (
        <div className={classes.flex}>
            <Grid container justify='center' direction="row" spacing={16}>
                {videos}
            </Grid>
        </div>
    )
}

VideoListDummyTemplate.propTypes = {
    classes: PropTypes.any,
    videos: PropTypes.array.isRequired,
}

export default withStyles(styles)(VideoListDummyTemplate)
