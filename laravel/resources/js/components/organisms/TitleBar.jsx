import React from "react"
import { Link } from "react-router-dom"
import PropTypes from 'prop-types'
import { withStyles } from "@material-ui/core"
import AppBar from "@material-ui/core/AppBar/AppBar"
import Toolbar from "@material-ui/core/Toolbar/Toolbar"
import Typography from "@material-ui/core/Typography/Typography"


const styles = theme => ({
    appBar: {
        zIndex: theme.zIndex.drawer + 1,
        backgroundColor: "#424242",
    },
    title: {
        margin: "0 auto",
        textDecoration: "none"
    }
})

function TitleBar(props) {
    const {classes, title} = props;

    return (
        <AppBar className={classes.appBar} position="fixed">
            <Toolbar>
                <Typography className={classes.title} variant="title" color="inherit" noWrap
                            component={Link} to="/">
                    {title}
                </Typography>
            </Toolbar>
        </AppBar>
    )
}

TitleBar.propTypes = {
    classes: PropTypes.object.isRequired,
    title: PropTypes.string.isRequired,
}

export default withStyles(styles)(TitleBar)
