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
      // marginRight: -8,
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
    img: "https://i.ytimg.com/vi/AlZ3H-A2BeQ/default.jpg",
    title: "R-指定 UMB 3連覇達成＆Creepy Nuts本格始動 コメント",
    date: "2018-10-5"
  };

class NewSongs extends React.Component {

  render() {
    const { classes } = this.props;

    return (
      <div className={classes.flex}>
      <Typography variant="headline">
        最新曲
      </Typography>
      <Grid container justify='flex-start' direction="row" spacing="16">
        {/* {[0,1,2,3,4].map(value=> ( */}
          <Grid  item>
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
          <Grid  item>
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
          <Grid  item>
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
          <Hidden only={['sm']}>
          <Grid  item>
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
          </Hidden>

          <Hidden only={['sm', 'md']}>
            <Grid item>
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
          </Hidden>
      {/* ))} */}
    </Grid>
    </div>
    );
  }
}

NewSongs.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(NewSongs);