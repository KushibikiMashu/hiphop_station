import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import CssBaseline from '@material-ui/core/CssBaseline';
import SearchAppBar from './SearchAppBar';
import RecipeReviewCard from './RecipeReviewCard'

export default class Material extends Component {
    render() {
        return (
            <div className="test_container">
                <CssBaseline />
                <SearchAppBar />
                <RecipeReviewCard />
            </div>
        );
    }
}

if (document.getElementById('material')) {
    ReactDOM.render(<Material />, document.getElementById('material'));
}
