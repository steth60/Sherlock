import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import Tabs from './components/Tabs';

const instanceElement = document.getElementById('instance-details');
if (instanceElement) {
    const instance = JSON.parse(instanceElement.getAttribute('data-instance'));
    const root = createRoot(instanceElement); // Use createRoot from react-dom/client
    root.render(<Tabs instance={instance} />);
}
