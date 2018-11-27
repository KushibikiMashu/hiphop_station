import React from 'react';
import PropTypes from 'prop-types';
import {withStyles} from '@material-ui/core/styles';
import BottomNavigation from '@material-ui/core/BottomNavigation';
import BottomNavigationAction from '@material-ui/core/BottomNavigationAction';
import Icon from '@material-ui/core/Icon';
import FiberNewIcon from '@material-ui/icons/FiberNew';
import MusicVideoIcon from '@material-ui/icons/MusicVideo';
import NewReleasesIcon from '@material-ui/icons/NewReleases';
import PersonIcon from '@material-ui/icons/Person';
import LiveTvIcon from '@material-ui/icons/LiveTv';
import {BrowserRouter as Router, Route} from "react-router-dom";

const styles = {
    root: {
        width: '100%',
    },
};

class LabelBottomNavigationDummy extends React.Component {
    render() {
        const {classes} = this.props;
        const {value} = this.state;

        return (
            <BottomNavigation value={value} onChange={this.handleChange} className={classes.root}>
                <BottomNavigationAction icon={<FiberNewIcon/>}/>
                <BottomNavigationAction icon={<MusicVideoIcon/>}/>
                <BottomNavigationAction icon={<NewReleasesIcon/>}/>
            </BottomNavigation>
        );
    }
}

LabelBottomNavigationDummy.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(LabelBottomNavigationDummy);
