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
import Button from '@material-ui/core/Button';

import NavigationIcon from '@material-ui/icons/Navigation';
import { Link } from 'react-router-dom';

import request from 'superagent';
const PATH = "http://localhost:3000/json/songs.json";

const styles = theme => ({
  flex: {
    flexGrow: 1,
  },
  card: {
    maxWidth: 260,
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
  headline: {
    textAlign: 'center',
    marginTop: -8,
    paddingBottom: 12,
  },
  cardContent: {
    paddingTop: 8,
    paddingBottom: 4,
    paddingLeft: 12,
    paddingRight: 12,
  },
  root: {
    justifyContent: 'center'
  },
  button: {
    marginTop: 12,
  },
});

class NewSongs extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: null,
      hasMoreVideos: true,
      loadedVideosCount: 20, // デフォルトの動画表示数
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
    console.log(res.body);
    this.setState({
      items: res.body
    });
  };

  // 「LOAD MORE」ボタンをクリックすると、新たに10個の動画を表示する
  loadVideos() {
    console.log(this.state);
    if (this.state.loadedVideosCount >= this.state.items.length) {
      return;
    }

    this.setState({
      loadedVideosCount: this.state.loadedVideosCount + 10
    });
  }

  // loadVideos関数が呼ばれると、再度render関数が作動する
  render() {
    // asyncでres.bodyがstateに登録されるようにする
    if (!this.state.items) {
      return false;
    }

    const { classes } = this.props;

    // loadedVideosCountの数だけ動画を読み込む
    var videos = [];
    const items = this.state.items;
    for (var i = 0; i < this.state.loadedVideosCount; i++) {
      videos.push(
        <Grid item key={i}>
          <Card className={classes.card}>
            <CardMedia
              className={classes.media}
              image={items[i].img}
              component={Link}
              to={'/video/' + items[i].hash}
            />
            <a href={'/react/material/video/' + items[i].hash} style={{ textDecoration: "none" }}>
              <CardContent className={classes.cardContent}>
                <Typography gutterBottom variant="subheading">
                  {items[i].title}
                </Typography>
              </CardContent>
            </a>
            <CardActions>
              <Typography variant="caption">
                {items[i].channel}
              </Typography>
              <Typography variant="caption">
                {items[i].date}
              </Typography>
            </CardActions>
          </Card>
        </Grid>
      )
    }

    return (
      <div className={classes.flex}>
        <Typography variant="headline" className={classes.headline}>
          最新曲
        </Typography>
        <Grid container justify='center' direction="row" spacing={16}>
          {videos}
        </Grid>
        <Grid container justify='center' direction="row">
          <Button variant="extendedFab" aria-label="Load" className={classes.button} onClick={() => { this.loadVideos() }}>
            LOAD MORE
          </Button>
        </Grid>
      </div>
    );
  }
}

NewSongs.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(NewSongs);