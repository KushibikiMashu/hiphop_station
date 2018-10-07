import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import CssBaseline from '@material-ui/core/CssBaseline';
import SearchAppBar from './SearchAppBar';
import NewSongs from './NewSongs'
import NewMCBattle from './NewMCBattle'
import ClippedDrawer from './ClippedDrawer'

export default class Material extends Component {
    render() {
        return (
            <div className="test_container">
                <CssBaseline />
                <ClippedDrawer />
            </div>
        );
    }
}

if (document.getElementById('material')) {
    ReactDOM.render(<Material />, document.getElementById('material'));
}
