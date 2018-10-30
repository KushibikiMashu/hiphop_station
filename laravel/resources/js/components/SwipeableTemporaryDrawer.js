import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import SwipeableDrawer from '@material-ui/core/SwipeableDrawer';
import IconButton from '@material-ui/core/IconButton';
import MenuIcon from '@material-ui/icons/Menu';
import { DrawerListItems } from './DrawerListItems';

const styles = {
    drawer: {
        marginTop: 64,
    },
};

class SwipeableTemporaryDrawer extends React.Component {
    state = {
        top: false,
    };

    toggleDrawer = (side, open) => () => {
        this.setState({
            [side]: open,
        });
    };

    render() {

        return (
            <div>
                <IconButton
                    onClick={this.toggleDrawer('top', true)}
                    color="inherit"
                    aria-label="Open drawer"
                >
                    <MenuIcon />
                </IconButton>
                <SwipeableDrawer
                    anchor="top"
                    open={this.state.top}
                    onClose={this.toggleDrawer('top', false)}
                    onOpen={this.toggleDrawer('top', true)}
                    className={styles.drawer}
                >
                    <div
                        tabIndex={0}
                        role="button"
                        onClick={this.toggleDrawer('top', false)}
                        onKeyDown={this.toggleDrawer('top', false)}
                    >
                        {DrawerListItems}
                    </div>
                </SwipeableDrawer>
            </div>
        );
    }
}

SwipeableTemporaryDrawer.propTypes = {
    classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(SwipeableTemporaryDrawer);
