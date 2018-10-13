import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import classnames from 'classnames';
import Card from '@material-ui/core/Card';
import CardMedia from '@material-ui/core/CardMedia';
import CardContent from '@material-ui/core/CardContent';
import CardActions from '@material-ui/core/CardActions';
import Typography from '@material-ui/core/Typography';
import red from '@material-ui/core/colors/red';
import Grid from '@material-ui/core/Grid';
import Hidden from '@material-ui/core/Hidden';

import NavigationIcon from '@material-ui/icons/Navigation';
import { Link } from 'react-router-dom';

import request from 'superagent';
import MainVideo from './MainVideo';
import RecommendVideos from './RecommendVideos';

const PATH = "http://localhost:3000/json/songs.json";

const styles = theme => ({
    flex: {
        flexGrow: 1,
    },
    card: {
        // maxWidth: 560,
        // maxHeight: 600,
        justifyContent: 'center',
    },
    media: {
        height: 0,
        paddingTop: '56.25%', // 16:9
    },
    actions: {
        display: 'flex',
    },
    expand: {
        transform: 'rotate(0deg)',
        transition: theme.transitions.create('transform', {
            duration: theme.transitions.duration.shortest,
        }),
        marginLeft: 'auto',
        [theme.breakpoints.up('sm')]: {
            // marginRight: -8,
        },
    },
    expandOpen: {
        transform: 'rotate(180deg)',
    },
    cardContent: {
        paddingTop: 4,
        paddingBottom: 4,
        paddingLeft: 12,
        paddingRight: 12,
    },
    root: {
        justifyContent: 'center'
    }
});

class VideoPlayer extends React.Component {
    constructor(props) {
        super(props);
        // this.state = {
        //     items: null
        // };
    }

    // componentWillMount() {
    //     request.get(PATH)
    //         .end((err, res) => {
    //             this.loadedJson(err, res);
    //         });
    // };

    // loadedJson(err, res) {
    //     if (err) {
    //         console.log('JSON読み込みエラー');
    //         return;
    //     }
    //     console.log(res.body);
    //     this.setState({
    //         items: res.body
    //     });
    //     console.log(this.state.items);
    // };

    render() {
        // asyncでres.bodyがstateに登録されるようにする
        // if (!this.state.items) {
        //     return false;
        // }

        const { classes } = this.props;

        var path = location.pathname

        return (
            <React.Fragment>
                <MainVideo />
                {path}
                <RecommendVideos />
            </React.Fragment>
        );
    }
}

VideoPlayer.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(VideoPlayer);