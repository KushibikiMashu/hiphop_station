import React from 'react';
import ListItem from '@material-ui/core/ListItem';
import ListItemIcon from '@material-ui/core/ListItemIcon';
import ListItemText from '@material-ui/core/ListItemText';
import HomeIcon from '@material-ui/icons/HomeOutlined';
import MusicVideoIcon from '@material-ui/icons/MusicVideoOutlined';
import NewReleasesIcon from '@material-ui/icons/NewReleasesOutlined';
import VideoLibraryIcon from '@material-ui/icons/VideoLibraryOutlined';
import { Link } from 'react-router-dom';

export const DrawerListItems = (
  <div>
    <ListItem button component={Link} to={'/'}>
      <ListItemIcon>
        <HomeIcon />
      </ListItemIcon>
      <ListItemText primary="Home" />
    </ListItem>
    <ListItem button component={Link} to={'/video'}>
      <ListItemIcon>
        <MusicVideoIcon />
      </ListItemIcon>
      <ListItemText primary="New Videos" />
    </ListItem>
    <ListItem button component={Link} to={'/battle'}>
      <ListItemIcon>
        <NewReleasesIcon />
      </ListItemIcon>
      <ListItemText primary="MC Battle" />
    </ListItem>
    <ListItem button component={Link} to={'/channel'}>
      <ListItemIcon>
        <VideoLibraryIcon />
      </ListItemIcon>
      <ListItemText primary="Channel" />
    </ListItem>
  </div>
);