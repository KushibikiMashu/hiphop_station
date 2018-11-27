import React from 'react';
import PropTypes from 'prop-types';
import {withStyles} from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import Toolbar from '@material-ui/core/Toolbar';
import Typography from '@material-ui/core/Typography';
import {grey900} from '@material-ui/core/colors'
import {BrowserRouter as Router, Route, Link} from 'react-router-dom';
import NewSongs from './NewSongs'
import VideoPlayer from './VideoPlayer'
import request from 'superagent';
import {pathToJson} from './const';
import Grid from "@material-ui/core/Grid/Grid";
import LabelBottomNavigation from "./LabelBottomNavigation";
import GenreVideo from "./GenreVideo";

const PATH = pathToJson("main");

const styles = theme => ({
    root: {
        flexGrow: 1,
        zIndex: 1,
        overflow: 'hidden',
        position: 'relative',
        display: 'flex',
    },
    appBar: {
        zIndex: theme.zIndex.drawer + 1,
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
    nav: {
        justify: 'center'
    },
    labelTopNavigation: {
        bottom: 0,
        position: 'fixed',
    }
});

class ClippedDrawer extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            items: null
        };
    }

    // コンポーネントがマウントする前に動画情報のJSONを読み込む
    componentWillMount() {
        request.get(PATH)
            .end((err, res) => {
                this.loadedJson(err, res);
            });
    };

    // 読み込んだ全ての動画情報を配列でitemsに格納
    loadedJson(err, res) {
        if (err) {
            console.log('JSON読み込みエラー');
            return;
        }
        this.setState({
            items: res.body
        });
    };

    render() {
        const {classes} = this.props;

        return (
            <Router basename="/">
                <div className={classes.root}>
                    <AppBar position="fixed" className={classes.appBar} style={{backgroundColor: '#424242'}}>
                        <Toolbar>
                            <Typography variant="title" color="inherit" noWrap
                                        style={{margin: "0 auto", textDecoration: "none"}} component={Link} to="/">
                                日本語ラップStation
                            </Typography>
                        </Toolbar>
                    </AppBar>
                    <main className={classes.content}>
                        <div className={classes.toolbar}/>
                        <Route exact path='/' component={NewSongs}/>
                        <Route path='/video/:hash' render={() => <VideoPlayer videos={this.state.items}/>}/>
                        <Route path='/music_video' render={() => <GenreVideo genre='MV'/>}/>
                        <Route path='/battle' render={() => <GenreVideo genre='battle'/>}/>
                        <Route path='/interview' render={() => <GenreVideo genre='interview'/>}/>
                        <Route path='/others' render={() => <GenreVideo genre='others'/>}/>
                    </main>
                    <Grid container justify='center' className={classes.labelTopNavigation}>
                        <LabelBottomNavigation/>
                    </Grid>
                </div>
            </Router>
        );
    }
}

ClippedDrawer.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(ClippedDrawer);
