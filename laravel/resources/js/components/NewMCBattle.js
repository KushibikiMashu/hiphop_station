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
    //   marginRight: -8,
    },
  },
  expandOpen: {
    transform: 'rotate(180deg)',
  },
  avatar: {
    backgroundColor: red[500],
  },
  root: {
    justifyContent: 'center'
  }
});

const videoData = 
  {
    img: "https://i.ytimg.com/vi/5Q_anTqs-_8/default.jpg",
    title: "裂固 vs GADORO/戦極MCBATTLE第15章(2016.11.06)@BEST BOUT2",
    date: "2017-02-17 12:18:43"
  };

class NewMCBattle extends React.Component {

  render() {
    const { classes } = this.props;

    return (
      <div className={classes.flex}>
      <Typography variant="headline">
        MCバトル
      </Typography>
      <Grid container justify='flex-start' direction="row" spacing="16">
        {[0,1,2,3,4].map(value=> (
          <Grid key={value} item>
            <Card className={classes.card}>
              <CardMedia
                className={classes.media}
                image={videoData.img}
              />
              <CardContent>
                <Typography gutterBottom variant="subheading">
                 {videoData.title}
                </Typography>
              </CardContent>
                <CardActions>
                <Typography gutterBottom variant="caption">
                 {videoData.date}
                </Typography>
                </CardActions>
              
            </Card>
          </Grid>
      ))}
    </Grid>
    </div>
    );
  }
}

NewMCBattle.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(NewMCBattle);