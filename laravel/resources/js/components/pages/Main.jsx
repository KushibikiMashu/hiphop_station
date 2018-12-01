import React from 'react'
import PropTypes from 'prop-types'
import ReactDOM from 'react-dom'
import {grey900} from '@material-ui/core/colors'
import {withStyles} from '@material-ui/core/styles'
import {BrowserRouter as Router} from 'react-router-dom'
import request from 'superagent'
import {pathToJson} from '../const'
import Grid from "@material-ui/core/Grid/Grid"
import LabelBottomNavigation from "../organisms/LabelBottomNavigation"
import TitleBar from "../organisms/TitleBar"
import Routing from "../organisms/Routing"
import Header from "../organisms/Header"

const PATH = pathToJson("main")

const styles = theme => ({
    root: {
        flexGrow: 1,
        zIndex: 1,
        overflow: 'hidden',
        position: 'relative',
        display: 'flex',
    },
    content: {
        flexGrow: 1,
        backgroundColor: theme.palette.background.default,
        paddingTop: theme.spacing.unit * 3,
        paddingBottom: theme.spacing.unit * 3,
        minWidth: 0, // So the Typography noWrap works
    },
    toolbar: {
        height: 52
    },
    labelBottomNavigation: {
        bottom: 0,
        position: 'fixed',
    }
})

class Main extends React.Component {
    constructor(props) {
        super(props)
        this.state = {
            items: null
        }
    }

    // コンポーネントがマウントする前に動画情報のJSONを読み込む
    componentWillMount() {
        request.get(PATH)
            .end((err, res) => {
                this.loadedJson(err, res)
            })
    }

    // 読み込んだ全ての動画情報を配列でitemsに格納
    loadedJson(err, res) {
        if (err) {
            console.log('JSON読み込みエラー')
            return
        }
        this.setState({
            items: res.body
        })
    }

    render() {
        const {classes} = this.props
        const title = "HIPSTY"
        let items = this.state.items
        if (items === null) {
            items = []
        }

        return (
            <Router basename="/">
                <div className={classes.root}>
                    <TitleBar title={title}/>
                    <main className={classes.content}>
                        <div className={classes.toolbar}/>
                        <Routing videos={items}/>
                    </main>
                    <Grid container justify='center' className={classes.labelBottomNavigation}>
                        <LabelBottomNavigation/>
                    </Grid>
                </div>
            </Router>
        )
    }
}

Main.propTypes = {
    classes: PropTypes.object.isRequired,
}

export default withStyles(styles)(Main);
