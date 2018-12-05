import React from "react"
import PropTypes from 'prop-types'
import { CONST } from "../const"

export default function TwitterIcon(props) {
    const {hash} = props
    return (
        <div>
            <a href="https://twitter.com/share?ref_src=twsrc%5Etfw"
               className="twitter-share-button"
               data-text={CONST.siteTitle}
               data-url={CONST.videoUrl + '/' + hash}
               data-hashtags="HIPHOP"
               data-show-count="false">
                <i className="fab fa-twitter fa-lg" style={{color: "#1da1f2"}}></i>
            </a>
            <script async src="https://platform.twitter.com/widgets.js" charSet="utf-8"></script>
        </div>
    )
}

TwitterIcon.propTypes = {
    hash: PropTypes.string.isRequired,
}
