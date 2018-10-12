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
const PATH = "http://localhost:3000/json/songs.json";

const styles = theme => ({
  flex: {
    flexGrow: 1,
    marginLeft: 20,
    marginBottom: 20,
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
  expandOpen: {
    transform: 'rotate(180deg)',
  },
  cardContent: {
    paddingTop: 4,
    paddingBottom: 4,
    paddingLeft: 12,
    paddingRight: 12,
    // height: 86,
  },
  root: {
    justifyContent: 'center'
  }
});

class NewSongs extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      items: null
    };
  }

  componentWillMount() {
    request.get(PATH)
      .end((err, res) => {
        this.loadedJson(err, res);
      });
  };

  loadedJson(err, res) {
    if (err) {
      console.log('JSON読み込みエラー');
      return;
    }
    console.log(res.body);
    this.setState({
      items: res.body
    });
    console.log(this.state.items);
  };

  render() {
    // asyncでres.bodyがstateに登録されるようにする
    if (!this.state.items) {
      return false;
    }

    var i = 0;

    const { classes } = this.props;
    const songs = this.state.items.map(e => {
      i++;

      if(i > 20){
        return false;
      }

      return <Grid item key={i}> 
        <Card className={classes.card}>
          <CardMedia
            className={classes.media}
            image={e.img}
            component={Link}
            to={'/video/' + e.hash}
          />
          <CardContent className={classes.cardContent}>
            <Typography gutterBottom variant="subheading">
              {e.title}
            </Typography>
          </CardContent>
          <CardActions>
          <Typography variant="caption">
              {e.channel}
            </Typography>
            <Typography variant="caption">
              {e.date}
            </Typography>
          </CardActions>
        </Card>
      </Grid>
    });

    return (
      <div className={classes.flex}>
        <Typography variant="headline">
          最新曲
      </Typography>
        <Grid container justify='center' direction="row" spacing={16}>
          {songs}
        </Grid>
     </div>
    );
  }
}

NewSongs.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(NewSongs);