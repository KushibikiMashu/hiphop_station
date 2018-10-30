import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Card from '@material-ui/core/Card';
import CardMedia from '@material-ui/core/CardMedia';
import CardContent from '@material-ui/core/CardContent';
import CardActions from '@material-ui/core/CardActions';
import Typography from '@material-ui/core/Typography';
import Grid from '@material-ui/core/Grid';
import request from 'superagent';
const PATH = "http://ec2-54-163-220-138.compute-1.amazonaws.com/json/battle.json";

const styles = theme => ({
  flex: {
    flexGrow: 1,
    marginLeft: 20,
    marginBottom: 20,
  },
  card: {
    maxWidth: 210,
  },
  media: {
    height: 0,
    paddingTop: '56.25%', // 16:9
  },
  cardContent: {
    paddingTop: 4,
    paddingBottom: 4,
    paddingLeft: 12,
    paddingRight: 12,
  },
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
      return;
    }
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

    const { classes } = this.props;
    const songs = this.state.items.map(e => {
      return (
      <Grid item>
        <Card className={classes.card}>
          <CardMedia
            className={classes.media}
            image={e.img}
          />
          <CardContent className={classes.cardContent}>
            <Typography gutterBottom variant="subheading">
              {e.title}
            </Typography>
          </CardContent>
          <CardActions>
            <Typography gutterBottom variant="caption">
              {e.date}
            </Typography>
          </CardActions>
        </Card>
      </Grid>
      )
    });

    return (
      <div className={classes.flex}>
        <Typography variant="headline">
          MCバトル
      </Typography>
        <Grid container justify='center' direction="row" spacing="16">
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
