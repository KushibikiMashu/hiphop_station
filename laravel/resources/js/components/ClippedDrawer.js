import React from 'react';
import PropTypes from 'prop-types';
import {withStyles} from '@material-ui/core/styles';
import {grey900} from '@material-ui/core/colors'
import {BrowserRouter as Router, Route, Link} from 'react-router-dom';
import VideoPlayer from './VideoPlayer'
import request from 'superagent';
import {pathToJson} from './const';
import Grid from "@material-ui/core/Grid/Grid";
import LabelBottomNavigation from "./organisms/LabelBottomNavigation";
import GenreVideo from "./GenreVideo";
import TitleBar from "./organisms/TitleBar";
import LandingPage from "./pages/LandingPage";

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

TitleBar.propTypes = {classes: PropTypes.any};

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
                    <TitleBar title='HIPSTY'/>
                    <main className={classes.content}>
                        <div className={classes.toolbar}/>
                        <Route exact path='/' component={LandingPage}/>
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
