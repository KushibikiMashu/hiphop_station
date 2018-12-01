import React from 'react';
import PropTypes from "prop-types";
import withWidth, { isWidthUp } from "@material-ui/core/withWidth";

class ResponsiveIframe extends React.Component {
    constructor(props){
        super(props);
    }

    render() {
        const { src, width } = this.props;

        const isSmallScreen = /xs/.test(width);
        const ifremeProps = {
            size: isSmallScreen ? "small" : "large",
            // width: isSmallScreen ? "100%" : 640,
            width: isSmallScreen ? "100%" : 640,
            height: isSmallScreen ? 280 : 460
        };

         // 条件式でwidth/heightを変更する
         // 参考 https://codesandbox.io/s/l0x8kqz7q?module=%2Fdemo.js

        if (isWidthUp('sm', width)) {
            return (
                <iframe src={src} {...ifremeProps} frameBorder="0" allow="autoplay; encrypted-media" allowFullScreen></iframe>
            );
        }

        return (
            <iframe src={src} {...ifremeProps} frameBorder="0" allow="autoplay; encrypted-media" allowFullScreen></iframe>
        );
    };
}

ResponsiveIframe.propTypes = {
    width: PropTypes.string.isRequired
};

export default withWidth()(ResponsiveIframe);
