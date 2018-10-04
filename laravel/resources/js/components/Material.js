import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import PersistentDrawer from './PersistentDrawer';

export default class Material extends Component {
    render() {
        return (
            <div className="test_container">
                <PersistentDrawer />
            </div>
        );
    }
}

if (document.getElementById('material')) {
    ReactDOM.render(<Material />, document.getElementById('material'));
}
