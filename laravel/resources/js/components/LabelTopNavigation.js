import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import BottomNavigation from '@material-ui/core/BottomNavigation';
import BottomNavigationAction from '@material-ui/core/BottomNavigationAction';
import Icon from '@material-ui/core/Icon';
import FiberNewIcon from '@material-ui/icons/FiberNew';
import MusicVideoIcon from '@material-ui/icons/MusicVideo';
import NewReleasesIcon from '@material-ui/icons/NewReleases';
import PersonIcon from '@material-ui/icons/Person';
import LiveTvIcon from '@material-ui/icons/LiveTv';

const styles = {
    root: {
        width: 600,
    },
};

class LabelTopNavigation extends React.Component {
    state = {
        value: 'new',
    };

    handleChange = (event, value) => {
        this.setState({ value });
    };

    render() {
        const { classes } = this.props;
        const { value } = this.state;

        return (
            <BottomNavigation value={value} onChange={this.handleChange} className={classes.root}>
                <BottomNavigationAction label="New Arrival" value="new" icon={<FiberNewIcon />} />
                <BottomNavigationAction label="Music Video" value="music video" icon={<MusicVideoIcon />} />
                <BottomNavigationAction label="Battle" value="battle" icon={<NewReleasesIcon/>} />
                {/*<BottomNavigationAction label="Interview" value="interview" icon={<PersonIcon/>} />*/}
                {/*<BottomNavigationAction label="Others" value="others" icon={<LiveTvIcon/>} />*/}
            </BottomNavigation>
        );
    }
}

LabelTopNavigation.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(LabelTopNavigation);
