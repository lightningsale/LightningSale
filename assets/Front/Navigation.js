import React from "react";

export default class Navigation extends React.Component {
    render() {
        return (
            <ul className="nav flex-column">
                <li className="nav-item text-white sidebar-brand">
                    <span><i className="fa fa-bolt fa-2x" aria-hidden="true"/></span>
                    <span>Lightning.Sale</span>
                </li>
                <li className="nav-item sidebar-text small">
                    Today
                </li>
                <li className="nav-item sidebar-sale">
                    <h1>0.23
                        <small><strong>BTC</strong></small>
                    </h1>
                </li>
                <li className="nav-item sidebar-text text-white">1 <i
                    className="fa fa-btc"/> 114.152,64 NOK
                </li>
                <li className="nav-item sidebar-break"/>
                <li className="nav-item"><a href="#">Dashboard</a></li>
                <li className="nav-item"><a href="#">Wallet</a></li>
                <li className="nav-item"><a href="#">Users</a></li>
                <li className="nav-item"><a href="#">Info</a></li>
                <li className="nav-item"><a href="#">Profile</a></li>
                <li className="nav-item"><a href="#">Sign out</a></li>
            </ul>
        )
    }
}