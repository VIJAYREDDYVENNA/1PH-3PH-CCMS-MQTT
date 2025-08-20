<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
$sessionVars = SessionManager::SessionVariables();

$mobile_no = $sessionVars['mobile_no'];
$user_id = $sessionVars['user_id'];
$role = $sessionVars['role'];
$user_login_id = $sessionVars['user_login_id'];
$user_name = $sessionVars['user_name'];
$user_email = $sessionVars['user_email'];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="auto">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    .container {
      margin: 0 auto;
    }

    .section-title {
      padding-left: 1rem;
      border-left: 4px solid #6366f1;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .section-title.purple {
      border-color: #8b5cf6;
    }

    .section-title.blue {
      border-color: #3b82f6;
    }

    .section-title.amber {
      border-color: #f59e0b;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1.5rem;
    }

    @media (max-width: 767px) {
      .grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
    }

    .card {
      background: #ffffff;
      border-radius: 0.5rem;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      animation: fadeInUp 0.6s ease forwards;
      opacity: 0;
      transform: translateY(20px);
    }

    .card-header {
      padding: 1rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .card-header h3 {
      font-size: 1.1rem;
      font-weight: 600;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .card-header::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
      transform: rotate(45deg);
      pointer-events: none;
    }

    .card-body {
      padding: 1rem;
    }

    .purple-gradient {
      background: linear-gradient(135deg, #8b5cf6, #6d28d9);
    }

    .blue-gradient {
      background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .cyan-gradient {
      background: linear-gradient(135deg, #06b6d4, #14b8a6);
    }

    .amber-gradient {
      background: linear-gradient(135deg, #f97316, #f59e0b);
    }

    .purple-light-bg {
      background: linear-gradient(to bottom right, #f5f3ff, #ede9fe);
    }

    .blue-light-bg {
      background: linear-gradient(to bottom right, #e0f2fe, #bae6fd);
    }

    .cyan-light-bg {
      background: linear-gradient(to bottom right, #cffafe, #a5f3fc);
    }

    .amber-light-bg {
      background: linear-gradient(to bottom right, #fef3c7, #fde68a);
    }

    .data-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0;
      border-bottom: 1px solid #e2e8f0;
    }

    .data-row:last-child {
      border-bottom: none;
    }

    .data-label {
      font-weight: 500;
    }

    .data-value {
      font-weight: 600;
    }

    .badge-success {
      color: white;
      padding: 8px;
      border-radius: 10px;
      font-weight: bold;
    }

    .badge-danger {
      color: white;
      padding: 8px;
      border-radius: 10px;
      font-weight: bold;
    }

    .badge-outline-success {
      background-color: rgba(16, 185, 129, 0.1);
      color: green;
      border: 1px solid rgba(16, 185, 129, 0.2);
      padding: 5px;
      border-radius: 10px;
      font-weight: bold;
    }

    .badge-outline-danger {
      background-color: rgba(239, 68, 68, 0.1);
      color: red;
      border: 1px solid rgba(239, 68, 68, 0.2);
      padding: 5px;
      border-radius: 10px;
      font-weight: bold;
    }

    .flow-meter {
      margin-top: 0.5rem;
      height: 0.5rem;
      background-color: #e2e8f0;
      border-radius: 0.25rem;
      overflow: hidden;
      position: relative;
    }

    .flow-meter-bar-on {
      position: absolute;
      height: 100%;
      width: 0;
      background: linear-gradient(90deg, #818cf8, #6366f1);
      border-radius: 0.25rem;
      animation: underlineAnimation 5s linear infinite;
    }

    .flow-meter-bar-off {
      position: absolute;
      height: 100%;
      width: 0;
      background: linear-gradient(90deg, #818cf8, #6366f1);
      border-radius: 0.25rem;
    }

    @keyframes underlineAnimation {
      0% {
        width: 0;
        left: 0;
      }

      40% {
        width: 100%;
        left: 0;
      }

      60% {
        width: 100%;
        left: 0;
      }

      100% {
        width: 0;
        left: 100%;
      }
    }

    .center-content {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100%;
    }

    .operation-mode {
      font-size: 1.5rem;
      font-weight: 600;
      text-align: center;
      color: #6d28d9;
    }

    .operation-mode-subtitle {
      font-size: 0.875rem;
      color: #7c3aed;
      margin-top: 0.5rem;
    }

    .status-container {
      position: relative;
      display: inline-block;
    }

    .pulse-dot {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 70px;
      height: 70px;
      background-color: transparent;
      border-radius: 50%;
      transform: translate(-50%, -50%);
      animation: pulse 2s infinite;
      z-index: 0;
    }

    .status-content {
      position: relative;
      z-index: 1;
      display: inline-flex;
      align-items: center;
    }

    @keyframes pulse {
      0% {
        transform: translate(-50%, -50%) scale(0.95);
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
      }

      70% {
        transform: translate(-50%, -50%) scale(1);
        box-shadow: 0 0 0 5px rgba(255, 255, 255, 0);
      }

      100% {
        transform: translate(-50%, -50%) scale(0.95);
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
      }
    }

    .badge-danger .pulse-dot {
      background-color: #dc3545;
    }

    .operation-mode .pulse-dot {
      background-color: #7b68ee;
    }

    .pulse-dots {
      display: inline-block;
      width: 8px;
      height: 8px;
      background-color: currentColor;
      border-radius: 50%;
      margin-left: 0.5rem;
      animation: pulse 2s infinite;
      vertical-align: middle;
      position: relative;
    }

    .badge-success .pulse-dots {
      background-color: #ffffff;
    }

    .badge-outline-success .pulse-dots {
      background-color: #28a745;
    }

    .operation-mode .pulse-dots {
      background-color: #7b68ee;
    }

    .tabs {
      margin-bottom: 1rem;
    }

    .tabs-list {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
      gap: 0.5rem;
      margin-bottom: 1rem;
    }

    .tab-trigger {
      padding: 0.5rem;
      border: 1px solid #e2e8f0;
      border-radius: 0.25rem;
      font-weight: 500;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .tab-trigger.active {
      background-color: #f59e0b;
      color: white;
      border-color: #f59e0b;
    }

    .small-card {
      border-radius: 0.25rem;
      padding: 0.75rem;
      text-align: center;
    }

    .small-card-label {
      font-size: 1rem;
      margin-bottom: 0.25rem;
    }

    .battery-icon {
      font-size: 1.3rem;
    }

    .grid-3-cols {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
    }

    .grid-2-cols-equal {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
        opacity: 1;
      }

      70% {
        box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        opacity: 0.7;
      }

      100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        opacity: 1;
      }
    }

    .bg-grid-pattern {
      background-image: linear-gradient(to right, rgba(99, 102, 241, 0.1) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(99, 102, 241, 0.1) 1px, transparent 1px);
      background-size: 20px 20px;
    }

    /* Animation delays for staggered appearance */
    .grid .card:nth-child(1) {
      animation-delay: 0.1s;
    }

    .grid .card:nth-child(2) {
      animation-delay: 0.2s;
    }

    .grid .card:nth-child(3) {
      animation-delay: 0.3s;
    }

    .grid .card:nth-child(4) {
      animation-delay: 0.4s;
    }

    .grid .card:nth-child(5) {
      animation-delay: 0.5s;
    }

    .grid .card:nth-child(6) {
      animation-delay: 0.6s;
    }

    .hidden {
      display: none;
    }

    /* New styles for the table-like layout */
    .dashboard-container {
      margin: 0px auto;
      border-radius: 12px;
      overflow: hidden;
    }

    .dashboard-header {
      background: linear-gradient(to right, #6366f1, #818cf8);
      color: white;
      padding: 1.5rem 2rem;
    }

    .dashboard-title {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .dashboard-subtitle {
      font-size: 14px;
      opacity: 0.9;
    }

    .dashboard-content {
      padding: 1.5rem 2rem;
    }

    .details-row {
      display: flex;
      gap: 30px;
      margin-bottom: 30px;
    }

    @media (max-width: 768px) {
      .details-row {
        flex-direction: column;
      }
    }

    .details-column {
      flex: 1;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
      display: flex;
      flex-direction: column;
    }

    .table-container {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .table-like {
      width: 100%;
      display: flex;
      flex-direction: column;
      flex: 1;
    }

    .table-content {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .table-row {
      display: flex;
      border-bottom: 1px solid;
      transition: background-color 0.2s;
    }

    .table-row:last-child {
      border-bottom: none;
    }

    .table-header {
      font-weight: 600;
      background-color: #f1f5f9;
      color: var(--slate-700);
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .table-cell {
      padding: 12px 15px;
      flex: 1;
      display: flex;
      align-items: center;
    }

    .grid-layout {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
      gap: 15px;
      padding: 20px;
      flex: 1;
    }

    /* Force 3 columns in grid for wider screens */
    @media (min-width: 768px) {
      .grid-layout {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    .grid-item {
      border-radius: 8px;
      padding: 15px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      transition: all 0.2s ease;
      border: 1px solid none;
    }

    .item-title {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 10px;
      font-weight: 500;
    }

    .item-status {
      margin-bottom: 6px;
    }

    .item-details {
      font-size: 8px;
    }

    @media (min-width:1500px) {
      .flow-value {
        font-size: 10px !important;
        font-weight: 600;
        color: var(--slate-700);
        margin-bottom: 2px;
      }

      .item-details {
        font-size: 12px;
      }
    }

    .badge-success {
      color: green;
      display: inline-flex;
      align-items: center;
      padding: 8px;
      border-radius: 8px;
      font-size: 16px;
      line-height: 1.2;
    }

    .badge-success i {
      margin-right: 4px;
      font-size: 18px;
      vertical-align: middle;
    }

    .badge-danger {
      color: red;
      display: inline-flex;
      align-items: center;
      padding: 8px;
      border-radius: 8px;
      font-size: 16px;
      line-height: 1.2;
    }

    .badge-danger i {
      margin-right: 4px;
      font-size: 18px;
      vertical-align: middle;
    }

    .flow-value {
      font-size: 10px;
      font-weight: 600;
      color: var(--slate-700);
      margin-bottom: 2px;
    }

    .flow-label {
      font-size: 12px;
      color: #475569;
    }

    .flow-inactive {
      color: var(--slate-400);
    }

    @media (max-width: 500px) {
      body {
        padding: 1rem;
      }

      .dashboard-header {
        padding: 1rem;
      }

      .dashboard-content {
        padding: 1rem;
      }

      .details-row {
        gap: 15px;
      }
    }

    @media (max-width: 425px) {
      .grid-layout {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
      animation: fadeIn 0.5s ease;
    }

    .grid-2-cols {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
    }

    .section-subtitle {
      font-size: 1.05rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0;
    }

    .metric-row {
      border-radius: 12px;
      padding: 1rem 1.4rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
      margin-bottom: 1.2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.3s ease;
      border-left: 4px solid transparent;
    }

    /* Custom hover effects for each metric type */
    .metric-row.r_y_voltage:hover {
      background-color: rgba(239, 68, 68, 0.1);
      border-left: 4px solid #ef4444;
    }

    .metric-row.y_b_voltage:hover {
      background-color: rgba(245, 158, 11, 0.1);
      border-left: 4px solid #f59e0b;
    }

    .metric-row.b_r_voltage:hover {
      background-color: rgba(59, 130, 246, 0.1);
      border-left: 4px solid #3b82f6;
    }

    .metric-row.current:hover {
      background-color: rgba(239, 68, 68, 0.1);
      border-left: 4px solid #ef4444;
    }

    .metric-row.energy:hover {
      background-color: rgba(16, 185, 129, 0.1);
      border-left: 4px solid #10b981;
    }

    .metric-row.frequency:hover {
      background-color: rgba(6, 182, 212, 0.1);
      border-left: 4px solid #06b6d4;
    }

    .metric-row.speed:hover {
      background-color: rgba(59, 130, 246, 0.1);
      border-left: 4px solid #3b82f6;
    }

    .metric-row.hours:hover {
      background-color: rgba(107, 114, 128, 0.1);
      border-left: 4px solid #6b7280;
    }

    .metric-row.refVoltage:hover {
      background-color: rgba(192, 132, 252, 0.1);
      border-left: 4px solid #c084fc;
    }

    .metric-row.refFrequency:hover {
      background-color: rgba(34, 197, 94, 0.1);
      border-left: 4px solid #22c55e;
    }

    .small-card-value {
      font-weight: 600;
      font-size: 1.2rem;
      padding: 0.4rem 1rem;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      min-width: 100px;
      text-align: center;
    }

    .metric-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 42px;
      height: 42px;
      border-radius: 10px;
      margin-right: 12px;
      color: white;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .icon-r_y_voltage {
      background-color: #ef4444;
    }

    .icon-y_b_voltage {
      background-color: #f59e0b;
    }

    .icon-b_r_voltage {
      background-color: #3b82f6;
    }

    .icon-refVoltage {
      background-color: #c084fc;
    }

    .icon-refFrequency {
      background-color: #22c55e;
    }

    .icon-current {
      background-color: #ef4444;
    }

    .icon-energy {
      background-color: #10b981;
    }

    .icon-frequency {
      background-color: #06b6d4;
    }

    .icon-speed {
      background-color: #3b82f6;
    }

    .icon-hours {
      background-color: #6b7280;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .grid-2-cols {
        grid-template-columns: 1fr;
      }
    }

    .admin-only-metric {
      margin-top: 1rem;
      padding-top: 1rem;
    }

    .admin-value {
      color: #0056b3;
      font-weight: bold;
    }

    .icon-admin-only {
      background: linear-gradient(45deg, #5e35b1, #3949ab);
    }

    .admin-only-metric:hover {
      background-color: rgba(59, 130, 246, 0.1);
      border-left: 4px solid #0056b3;
    }

    .timestamp-value {
      color: #00468C;
      font-weight: 600;
    }

    :root {
      --primary: #2196f3;
      --primary-light: #64b5f6;
      --primary-dark: #1976d2;
      --success: #4caf50;
      --danger: #f44336;
      --dark: #263238;
      --light: #eceff1;
      --gray: #607d8b;
      --gray-light: #b0bec5;
      --gray-dark: #455a64;
    }

    /* Water System Visualization Styles */
    .visualization-container {
      width: 100%;
      max-width: 1600px;
      margin: 0 auto 60px auto;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      position: relative;
    }

    .system-layout {
      display: grid;
      grid-template-rows: auto auto;
      gap: 20px;
      position: relative;
    }

    /* Motors section */
    .motors-section {
      position: relative;
    }

    .motors-row {
      display: flex;
      justify-content: space-around;
      margin-bottom: 20px;
      padding: 0 40px;
    }

    .motor-unit {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100px;
    }

    .motor {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #78909c 0%, #546e7a 100%);
      border-radius: 50%;
      border: 4px solid #455a64;
      display: flex;
      justify-content: center;
      align-items: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      position: relative;
      margin-bottom: 10px;
    }

    .motor-inner {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #546e7a 0%, #455a64 100%);
      border-radius: 50%;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .motor-blade {
      position: absolute;
      width: 6px;
      height: 25px;
      background-color: #eceff1;
      top: 7.5px;
      left: 17px;
      transform-origin: center 12.5px;
      border-radius: 3px;
    }

    .motor.active .motor-blade {
      animation: spin 0.5s linear infinite;
    }

    .motor-status {
      position: absolute;
      top: -5px;
      right: -5px;
      width: 16px;
      height: 16px;
      background-color: var(--danger);
      border-radius: 50%;
      border: 2px solid white;
      transition: background-color 0.3s;
    }

    .motor.active .motor-status {
      background-color: var(--success);
    }

    .motor-label {
      font-weight: bold;
      font-size: 14px;
      text-align: center;
      color: var(--dark);
    }

    .motor-connector {
      width: 10px;
      height: 50px;
      background-color: var(--gray-light);
      border-radius: 5px;
      position: relative;
    }

    .motor-connector.active {
      background-color: var(--primary);
      position: relative;
      overflow: hidden;
    }

    .motor-connector.active::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: linear-gradient(45deg,
          rgba(255, 255, 255, 0.3) 25%,
          transparent 25%,
          transparent 50%,
          rgba(255, 255, 255, 0.3) 50%,
          rgba(255, 255, 255, 0.3) 75%,
          transparent 75%,
          transparent);
      background-size: 20px 20px;
      animation: flow 1s linear infinite;
    }

    /* Main pipe */
    .main-pipe {
      position: relative;
      height: 20px;
      background-color: var(--gray-light);
      border-radius: 10px;
      margin: 0 20px;
    }

    .main-pipe.active {
      background-color: var(--primary);
      position: relative;
      overflow: hidden;
    }

    .main-pipe.active::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: linear-gradient(45deg,
          rgba(255, 255, 255, 0.3) 25%,
          transparent 25%,
          transparent 50%,
          rgba(255, 255, 255, 0.3) 50%,
          rgba(255, 255, 255, 0.3) 75%,
          transparent 75%,
          transparent);
      background-size: 20px 20px;
      animation: flow 1s linear infinite;
    }

    /* Branch pipes - CRITICAL FIX */
    .branch {
      position: absolute;
      width: 10px;
      height: 110px;
      background-color: var(--gray-light);
      top: 20px;
      transform: translateX(-50%);
      transition: background 0.3s;
      border-radius: 5px;
      /* Critical fix: Always show the pipes */
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      z-index: 20 !important;
    }

    .branch.active {
      background-color: var(--primary);
      position: relative;
      overflow: hidden;
    }

    .branch.active::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: linear-gradient(45deg,
          rgba(255, 255, 255, 0.3) 25%,
          transparent 25%,
          transparent 50%,
          rgba(255, 255, 255, 0.3) 50%,
          rgba(255, 255, 255, 0.3) 75%,
          transparent 75%,
          transparent);
      background-size: 20px 20px;
      animation: flow 1s linear infinite;
    }

    /* Valve styles - CRITICAL FIX */
    .valve {
      position: absolute;
      width: 30px;
      height: 30px;
      background: linear-gradient(135deg, #78909c 0%, #455a64 100%);
      border-radius: 50%;
      border: 3px solid #263238;
      transform: translateX(-50%);
      z-index: 30 !important;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
      cursor: pointer;
      transition: all 0.3s ease;
      /* Critical fix: Always show the valves */
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }

    .valve::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 4px;
      background-color: #eceff1;
      transform: translate(-50%, -50%) rotate(90deg);
      border-radius: 2px;
      transition: transform 0.5s ease;
    }

    .valve.open {
      background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%);
    }

    .valve.open::before {
      transform: translate(-50%, -50%) rotate(0deg);
    }

    /* Water flow animation through the valve */
    .valve.open::after {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      background-color: rgba(33, 150, 243, 0.3);
      transform: translate(-50%, -50%);
      animation: valvePulse 1.5s infinite;
    }

    /* Valve positions */
    .valve-1 {
      left: 16.6%;
      top: 75px;
    }

    .valve-2 {
      left: 33.2%;
      top: 75px;
    }

    .valve-3 {
      left: 49.8%;
      top: 75px;
    }

    .valve-4 {
      left: 66.4%;
      top: 75px;
    }

    .valve-5 {
      left: 83%;
      top: 75px;
    }

    .valve-6 {
      left: 99.6%;
      top: 75px;
    }

    /* Flow rate indicator */
    .flow-indicator {
      position: absolute;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 4px;
      padding: 2px 6px;
      font-size: 10px;
      font-weight: bold;
      color: var(--primary-dark);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      opacity: 0;
      transition: opacity 0.3s ease;
      pointer-events: none;
      z-index: 40 !important;
    }

    .branch.active+.flow-indicator {
      opacity: 1;
    }

    /* Platforms section */
    .platforms-section {
      margin-top: 110px;
      z-index: 10 !important;
    }

    .platforms-row {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 20px;
    }

    .platform-wrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
      min-width: 60px;
      z-index: 10 !important;
    }

    .platform {
      width: 60px;
      height: 150px;
      border: 2px solid #333;
      background: white;
      position: relative;
      overflow: hidden;
      border-radius: 5px;
      z-index: 10 !important;
    }

    .platform-fill {
      position: absolute;
      bottom: 0;
      width: 100%;
      height: 0%;
      background-color: var(--primary);
      transition: height 4s;
    }

    .platform-label {
      margin-bottom: 10px;
      font-size: 14px;
      font-weight: bold;
      text-align: center;
    }

    .splash {
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%) translateY(-40px);
      width: 20px;
      height: 20px;
      background: rgba(0, 134, 179, 0.91);
      border-radius: 50%;
      opacity: 0;
      animation: splashAnim 0.1s ease-out infinite;
      pointer-events: none;
    }

    /* Animations */
    @keyframes spin {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(360deg);
      }
    }

    @keyframes flow {
      from {
        background-position: 0 0;
      }

      to {
        background-position: 40px 0;
      }
    }

    @keyframes fillAnimation {
      from {
        height: 0%;
      }

      to {
        height: 95%;
      }
    }

    @keyframes splashAnim {
      0% {
        transform: translateX(-50%) translateY(-120px);
        width: 40px;
        height: 20px;
        opacity: 0.9;
      }

      100% {
        transform: translateX(-50%) translateY(0);
        width: 80px;
        height: 20px;
        opacity: 0;
      }
    }

    @keyframes valvePulse {
      0% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 0.7;
      }

      50% {
        transform: translate(-50%, -50%) scale(1.2);
        opacity: 0.3;
      }

      100% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 0.7;
      }
    }

    /* Media queries for responsiveness */
    @media (max-width: 992px) {
      .motor {
        width: 50px;
        height: 50px;
      }

      .motor-inner {
        width: 30px;
        height: 30px;
      }

      .motor-unit {
        width: 80px;
      }

      .platform {
        height: 120px;
      }

      .valve {
        width: 25px;
        height: 25px;
      }

      .valve::before {
        width: 16px;
        height: 3px;
      }
    }

    @media (max-width: 768px) {
      .motors-row {
        flex-wrap: wrap;
        gap: 20px;
      }

      .motor-unit {
        width: 30%;
        margin-bottom: 20px;
      }

      .platforms-row {
        gap: 10px;
      }

      .platform-wrapper {
        width: 30%;
      }
    }

    @media (max-width: 576px) {
      .visualization-container {
        padding: 10px;
      }

      .motor-unit {
        width: 45%;
      }

      .platform-wrapper {
        width: 45%;
      }

      .valve {
        width: 20px;
        height: 20px;
      }

      .valve::before {
        width: 12px;
        height: 2px;
      }
    }
  </style>
  <title>Dashboard</title>
  <?php
  include(BASE_PATH . "assets/html/start-page.php");
  ?>
  <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
    <div class="container-fluid">
      <div class="row mb-1 mt-1">
        <div class="col-12 ">
          <p class="breadcrumb-text text-muted m-0">
            <i class="bi bi-house-door-fill "></i> Pages / <span class="fw-medium ">Dashboard</span>
          </p>
        </div>
      </div>

      <div class="row mb-2">
        <div class="col-12 d-flex flex-column flex-md-row justify-content-md-end">
          <p class="m-0 mb-2 mb-md-0 me-md-3">
            <span class="text-body-tertiary">Connection Status: </span>
            <span id="connection_status" class="fw-bold"></span>
          </p>
          <p class="m-0" id="update_time">
            <span class="text-body-tertiary">Updated On: </span>
            <span id="auto_update_date_time"></span>
          </p>
        </div>
      </div>

      <div class="row">
        <div class="container">
          <!-- First Row -->
          <section class="section">
            <div class="grid">
              <!-- Operation Mode -->
              <div class="card">
                <div class="card-header purple-gradient">
                  <h3 class="text-light">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="3"></circle>
                      <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                    Operation Mode
                  </h3>
                </div>
                <div class="card-body p-0">
                  <div class="center-content purple-light-bg relative">
                    <div class="bg-grid-pattern absolute inset-0 opacity-5"></div>
                    <div class="relative z-10">
                      <h3 id="operation-mode-display" class="operation-mode">
                        <span class="pulse-dots"></span>
                      </h3>
                      <p id="operation-mode-subtitle" class="operation-mode-subtitle"></p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pipeline Pressure -->
              <div class="card">
                <div class="card-header blue-gradient">
                  <h3 class="text-light">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="10"></circle>
                      <path d="M16.2 7.8l-2 6.3-6.4 2.1 2-6.3z"></path>
                    </svg>
                    Pipeline Pressure
                  </h3>
                </div>
                <div class="card-body blue-light-bg">
                  <div class="data-row">
                    <span class="data-label data-label-color">Inlet Pressure:</span>
                    <span id="inlet-pressure-status" class="badge-outline-success fw-bold">
                      <span class="pulse-dots"></span>
                    </span>
                  </div>
                  <div class="data-row">
                    <span class="data-label data-label-color">Outlet Pressure 1:</span>
                    <span id="outlet-pressure-1" class="data-value data-label-color">0 kg/cm²</span>
                  </div>
                  <div class="data-row">
                    <span class="data-label data-label-color">Outlet Pressure 2:</span>
                    <span id="outlet-pressure-2" class="data-value data-label-color">0 kg/cm²</span>
                  </div>
                </div>
              </div>

              <!-- Total Flowrate & Frequencies -->
              <div class="card">
                <div class="card-header cyan-gradient">
                  <h3 class="text-light">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M3 3v18h18"></path>
                      <path d="M7 16h10"></path>
                    </svg>
                    Total Flowrate & Total Energy
                  </h3>
                </div>
                <div class="card-body cyan-light-bg">
                  <div class="data-row">
                    <span class="data-label data-label-color">Total Flowrate:</span>
                    <span id="total-flowrate" class="data-value data-label-color">0 L/min</span>
                  </div>
                  <div class="data-row">
                    <span class="data-label data-label-color">Total Cumulative Energy:</span>
                    <span id="total-energy" class="data-value data-label-color">0 kWh</span>
                  </div>
                  <div class="data-row">
                    <span class="data-label data-label-color">Total Pumped Water:</span>
                    <span id="total-water-pumped" class="data-value data-label-color">0 L</span>
                  </div>
                </div>
              </div>
            </div>
          </section>
          <div class="visualization-container mt-2">
            <div class="system-layout">
              <!-- Motors Section -->
              <div class="motors-section">
                <div class="motors-row">
                  <!-- Motor 1 -->
                  <div class="motor-unit">
                    <div class="motor-label">Motor 1</div>
                    <div class="motor" data-motor="1">
                      <div class="motor-inner">
                        <div class="motor-blade"></div>
                      </div>
                      <div class="motor-status"></div>
                    </div>
                    <div class="motor-connector" data-connector="1"></div>
                  </div>

                  <!-- Motor 2 -->
                  <div class="motor-unit">
                    <div class="motor-label">Motor 2</div>
                    <div class="motor" data-motor="2">
                      <div class="motor-inner">
                        <div class="motor-blade"></div>
                      </div>
                      <div class="motor-status"></div>
                    </div>
                    <div class="motor-connector" data-connector="2"></div>
                  </div>

                  <!-- Motor 3 -->
                  <div class="motor-unit">
                    <div class="motor-label">Motor 3</div>
                    <div class="motor" data-motor="3">
                      <div class="motor-inner">
                        <div class="motor-blade"></div>
                      </div>
                      <div class="motor-status"></div>
                    </div>
                    <div class="motor-connector" data-connector="3"></div>
                  </div>

                  <!-- Motor 4 -->
                  <div class="motor-unit">
                    <div class="motor-label">Motor 4</div>
                    <div class="motor" data-motor="4">
                      <div class="motor-inner">
                        <div class="motor-blade"></div>
                      </div>
                      <div class="motor-status"></div>
                    </div>
                    <div class="motor-connector" data-connector="4"></div>
                  </div>

                  <!-- Motor 5 -->
                  <div class="motor-unit">
                    <div class="motor-label">Motor 5</div>
                    <div class="motor" data-motor="5">
                      <div class="motor-inner">
                        <div class="motor-blade"></div>
                      </div>
                      <div class="motor-status"></div>
                    </div>
                    <div class="motor-connector" data-connector="5"></div>
                  </div>

                  <!-- Motor 6 -->
                  <div class="motor-unit">
                    <div class="motor-label">Motor 6</div>
                    <div class="motor" data-motor="6">
                      <div class="motor-inner">
                        <div class="motor-blade"></div>
                      </div>
                      <div class="motor-status"></div>
                    </div>
                    <div class="motor-connector" data-connector="6"></div>
                  </div>
                </div>

                <!-- Main Pipe -->
                <div class="main-pipe" id="mainPipe">
                  <!-- Branch Pipes -->
                  <div class="branch branch-1" data-branch="1"></div>
                  <div class="flow-indicator flow-indicator-1">0 L/min</div>
                  <div class="valve valve-1" data-valve="1"></div>

                  <div class="branch branch-2" data-branch="2"></div>
                  <div class="flow-indicator flow-indicator-2">0 L/min</div>
                  <div class="valve valve-2" data-valve="2"></div>

                  <div class="branch branch-3" data-branch="3"></div>
                  <div class="flow-indicator flow-indicator-3">0 L/min</div>
                  <div class="valve valve-3" data-valve="3"></div>

                  <div class="branch branch-4" data-branch="4"></div>
                  <div class="flow-indicator flow-indicator-4">0 L/min</div>
                  <div class="valve valve-4" data-valve="4"></div>

                  <div class="branch branch-5" data-branch="5"></div>
                  <div class="flow-indicator flow-indicator-5">0 L/min</div>
                  <div class="valve valve-5" data-valve="5"></div>

                  <div class="branch branch-6" data-branch="6"></div>
                  <div class="flow-indicator flow-indicator-6">0 L/min</div>
                  <div class="valve valve-6" data-valve="6"></div>
                </div>
              </div>

              <!-- Platforms Section -->
              <div class="platforms-section">
                <div class="platforms-row">
                  <!-- Platform 1 & 2 -->
                  <div class="platform-wrapper">
                    <div class="platform-label">Platform 1 & 2</div>
                    <div class="platform" data-platform="1">
                      <div class="platform-fill" data-fill="1"></div>
                    </div>
                  </div>

                  <!-- Platform 3 & 4 -->
                  <div class="platform-wrapper">
                    <div class="platform-label">Platform 3 & 4</div>
                    <div class="platform" data-platform="2">
                      <div class="platform-fill" data-fill="2"></div>
                    </div>
                  </div>

                  <!-- Platform 5 & 6 -->
                  <div class="platform-wrapper">
                    <div class="platform-label">Platform 5 & 6</div>
                    <div class="platform" data-platform="3">
                      <div class="platform-fill" data-fill="3"></div>
                    </div>
                  </div>

                  <!-- Platform 7 -->
                  <div class="platform-wrapper">
                    <div class="platform-label">Platform 7</div>
                    <div class="platform" data-platform="4">
                      <div class="platform-fill" data-fill="4"></div>
                    </div>
                  </div>

                  <!-- Platform 8 -->
                  <div class="platform-wrapper">
                    <div class="platform-label">Platform 8</div>
                    <div class="platform" data-platform="5">
                      <div class="platform-fill" data-fill="5"></div>
                    </div>
                  </div>

                  <!-- Platform 9 & 10 -->
                  <div class="platform-wrapper">
                    <div class="platform-label">Platform 9 & 10</div>
                    <div class="platform" data-platform="6">
                      <div class="platform-fill" data-fill="6"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="dashboard-container">

            <div class="details-row">
              <div class="details-column details-column-color card">
                <div class="details-title section-title"> <i class="bi bi-cpu-fill"></i> Motor Details</div>
                <div class="grid-layout">
                  <div class="grid-item grid-item-color ">
                    <div class="item-title">
                      <div>Motor 1</div>

                    </div>
                    <div class="item-status">
                      <div id="motor-1-status" class="badge-danger"><img src="../assets/photos/scr_images/off_motor.png" style="height: 40px;width:50px;margin-right:5px"> OFF</div>
                    </div>
                    <div id="motor-1-flow" class="flow-value">Flow Rate: 0 L/min</div>
                    <div class="item-details">Running from Last : <span id="motor-1-runtime" class="fw-bold">0 min</span></div>
                  </div>

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Motor 2</div>
                    </div>
                    <div class="item-status">
                      <span id="motor-2-status" class="badge-danger"><img src="../assets/photos/scr_images/off_motor.png" style="height: 40px;width:50px;margin-right:5px"> OFF</span>
                    </div>
                    <div id="motor-2-flow" class="flow-value flow-inactive">Flow Rate: 0.00 L/min</div>
                    <div class="item-details">Running from Last : <span id="motor-2-runtime" class="fw-bold">0 min</span></div>
                  </div>

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Motor 3</div>
                    </div>
                    <div class="item-status">
                      <span id="motor-3-status" class="badge-danger"><img src="../assets/photos/scr_images/off_motor.png" style="height: 40px;width:50px;margin-right:5px"> OFF</span>
                    </div>
                    <div id="motor-3-flow" class="flow-value flow-inactive">Flow Rate: 0.00 L/min</div>
                    <div class="item-details">Running from Last : <span id="motor-3-runtime" class="fw-bold">0 min</span></div>
                  </div>

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Motor 4</div>
                    </div>
                    <div class="item-status">
                      <span id="motor-4-status" class="badge-danger"><img src="../assets/photos/scr_images/off_motor.png" style="height: 40px;width:50px;margin-right:5px"> OFF</span>
                    </div>
                    <div id="motor-4-flow" class="flow-value flow-inactive">Flow Rate: 0.00 L/min</div>
                    <div class="item-details">Running from Last : <span id="motor-4-runtime" class="fw-bold">0 min</span></div>
                  </div>

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Motor 5</div>
                    </div>
                    <div class="item-status">
                      <span id="motor-5-status" class="badge-danger"><img src="../assets/photos/scr_images/off_motor.png" style="height: 40px;width:50px;margin-right:5px"> OFF</span>
                    </div>
                    <div id="motor-5-flow" class="flow-value flow-inactive">Flow Rate: 0.00 L/min</div>
                    <div class="item-details">Running from Last : <span id="motor-5-runtime" class="fw-bold">0 min</span></div>
                  </div>

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Motor 6</div>
                    </div>
                    <div class="item-status">
                      <span id="motor-6-status" class="badge-danger"><img src="../assets/photos/scr_images/off_motor.png" style="height: 40px;width:50px;margin-right:5px"> OFF</span>
                    </div>
                    <div id="motor-6-flow" class="flow-value flow-inactive">Flow Rate: 0.00 L/min</div>
                    <div class="item-details">Running from Last : <span id="motor-6-runtime" class="fw-bold">0 min</span></div>
                  </div>
                </div>
              </div>

              <div class="details-column details-column-color card">
                <div class="details-title section-title"> <i class="bi bi-stack"></i> Platforms Valve Details</div>
                <div class="grid-layout">

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Platform 1 & 2</div>
                    </div>
                    <div class="item-status">
                      <span id="platform-1-2-status" class="badge-danger"><img src="../assets/photos/scr_images/valvecopy.png" style="height: 60px;width:50px;margin-right:10px"> Closed</span>
                    </div>
                    <div class="item-details mt-2">Open from Last : <span id="platform-1-2-time" class="fw-bold">0 min</span></div>
                  </div>

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Platform 3 & 4</div>
                    </div>
                    <div class="item-status">
                      <span id="platform-3-4-status" class="badge-danger"><img src="../assets/photos/scr_images/valvecopy.png" style="height: 60px;width:50px;margin-right:5px"> Closed</span>
                    </div>
                    <div class="item-details mt-2">Open from Last : <span id="platform-3-4-time" class="fw-bold">0 min</span></div>
                  </div>

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Platform 5 & 6</div>
                    </div>
                    <div class="item-status">
                      <span id="platform-5-6-status" class="badge-danger"><img src="../assets/photos/scr_images/valvecopy.png" style="height: 60px;width:50px;margin-right:5px"> Closed</span>
                    </div>
                    <div class="item-details mt-2">Open from Last : <span id="platform-5-6-time" class="fw-bold">0 min</span></div>
                  </div>

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Platform 7</div>
                    </div>
                    <div class="item-status">
                      <span id="platform-7-status" class="badge-danger"><img src="../assets/photos/scr_images/valvecopy.png" style="height: 60px;width:50px;margin-right:5px"> Closed</span>
                    </div>
                    <div class="item-details mt-2">Open from Last : <span id="platform-7-time" class="fw-bold">0 min</span></div>
                  </div>

                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Platform 8</div>
                    </div>
                    <div class="item-status">
                      <span id="platform-8-status" class="badge-danger"><img src="../assets/photos/scr_images/valvecopy.png" style="height: 60px;width:50px;margin-right:5px"> Closed</span>
                    </div>
                    <div class="item-details mt-2">Open from Last : <span id="platform-8-time" class="fw-bold">0 min</span></div>
                  </div>


                  <div class="grid-item grid-item-color">
                    <div class="item-title">
                      <div>Platform 9 & 10</div>
                    </div>
                    <div class="item-status">
                      <span id="platform-9-10-status" class="badge-danger"><img src="../assets/photos/scr_images/valvecopy.png" style="height: 60px;width:50px;margin-right:5px"> Closed</span>
                    </div>
                    <div class="item-details mt-2">Open from Last : <span id="platform-9-10-time" class="fw-bold">0 min</span></div>
                  </div>

                </div>
              </div>
            </div>

          </div>
          <!-- Electrical Details Section -->
          <section id="electrical-section" class="section">
            <div class="details-title">Electrical Details</div>

            <div class="tabs">
              <div class="tabs-list mt-2" id="motor-tabs">
                <!-- Tabs will be generated here -->
              </div>

              <div id="motor-tab-contents">
                <!-- Tab contents will be generated here -->
              </div>
            </div>
          </section>
          <input type="hidden" id="user-role" value="<?php echo $role; ?>"></input>
        </div>




      </div>
    </div>
  </div>
  <script>
    // Critical fix for branch pipes and valves visibility
    document.addEventListener("DOMContentLoaded", function() {
      // Add this CSS to ensure branch pipes and valves are always visible
      const styleElement = document.createElement('style');
      styleElement.textContent = `
    /* Force branch pipes to be visible */
    .branch {
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      z-index: 20 !important;
      position: absolute !important;
      width: 10px !important;
      height: 110px !important;
      background-color: var(--gray-light) !important;
      top: 20px !important;
      transform: translateX(-50%) !important;
      border-radius: 5px !important;
    }
    
    /* Force valves to be visible */
    .valve {
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      z-index: 30 !important;
      position: absolute !important;
      width: 30px !important;
      height: 30px !important;
      background: linear-gradient(135deg, #78909c 0%, #455a64 100%) !important;
      border-radius: 50% !important;
      border: 3px solid #263238 !important;
      transform: translateX(-50%) !important;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2) !important;
    }
    
    /* Ensure valve positions */
    .valve-1 { left: 16.6% !important; top: 75px !important; }
    .valve-2 { left: 33.2% !important; top: 75px !important; }
    .valve-3 { left: 49.8% !important; top: 75px !important; }
    .valve-4 { left: 66.4% !important; top: 75px !important; }
    .valve-5 { left: 83% !important; top: 75px !important; }
    .valve-6 { left: 99.6% !important; top: 75px !important; }
    
    /* Ensure branch positions */
    .branch-1 { left: 16.6% !important; }
    .branch-2 { left: 33.2% !important; }
    .branch-3 { left: 49.8% !important; }
    .branch-4 { left: 66.4% !important; }
    .branch-5 { left: 83% !important; }
    .branch-6 { left: 99.6% !important; }
    
    /* Fix z-index for platforms to be below pipes and valves */
    .platform, .platform-wrapper {
      z-index: 10 !important;
    }
  `;
      document.head.appendChild(styleElement);

      // Function to ensure branch pipes and valves are created and visible
      function fixBranchPipesAndValves() {
        // Position branch pipes
        document.querySelectorAll('.branch').forEach((branch, i) => {
          const branchIndex = i + 1;
          const position = branchIndex * 16.6 - 8.3;
          branch.style.left = `${position}%`;
          branch.style.display = 'block';
          branch.style.visibility = 'visible';
          branch.style.opacity = '1';
          branch.style.zIndex = '20';
        });

        // Create valves if they don't exist
        const existingValves = document.querySelectorAll('.valve');
        if (existingValves.length !== 6) {
          // Remove existing valves if they're not complete
          existingValves.forEach(valve => valve.remove());

          // Create valves for each branch pipe
          document.querySelectorAll('.branch').forEach((branch, i) => {
            const branchIndex = i + 1;
            const systemLayout = document.querySelector('.system-layout');

            if (!systemLayout) return;

            // Create valve element
            const valve = document.createElement('div');
            valve.className = `valve valve-${branchIndex}`;
            valve.dataset.valve = branchIndex;

            // Position based on branch index
            const position = branchIndex * 16.6 - 8.3;
            valve.style.left = `${position}%`;
            valve.style.top = '75px';
            valve.style.display = 'block';
            valve.style.zIndex = '30';

            // Add valve to the system layout
            systemLayout.appendChild(valve);

            // Add valve indicator (horizontal line)
            const valveIndicator = document.createElement('div');
            valveIndicator.style.position = 'absolute';
            valveIndicator.style.top = '50%';
            valveIndicator.style.left = '50%';
            valveIndicator.style.width = '20px';
            valveIndicator.style.height = '4px';
            valveIndicator.style.backgroundColor = '#eceff1';
            valveIndicator.style.transform = 'translate(-50%, -50%) rotate(90deg)';
            valveIndicator.style.borderRadius = '2px';
            valve.appendChild(valveIndicator);

            // Create or update flow indicator
            let flowIndicator = document.querySelector(`.flow-indicator-${branchIndex}`);
            if (!flowIndicator) {
              flowIndicator = document.createElement('div');
              flowIndicator.className = `flow-indicator flow-indicator-${branchIndex}`;
              flowIndicator.style.left = `${position}%`;
              flowIndicator.style.top = '40px';
              flowIndicator.style.zIndex = '40';
              systemLayout.appendChild(flowIndicator);
            }

            flowIndicator.textContent = '0 L/min';
          });
        }

        // Make sure all valves are visible
        document.querySelectorAll('.valve').forEach(valve => {
          valve.style.display = 'block';
          valve.style.visibility = 'visible';
          valve.style.opacity = '1';
          valve.style.zIndex = '30';
        });
      }

      // Run the fix immediately
      fixBranchPipesAndValves();

      // Also run the fix after any animation updates
      const originalUpdateAnimation = window.updateAnimation;
      if (typeof originalUpdateAnimation === 'function') {
        window.updateAnimation = function() {
          const result = originalUpdateAnimation.apply(this, arguments);
          fixBranchPipesAndValves();
          return result;
        };
      }

      // Run the fix periodically to ensure visibility
      setInterval(fixBranchPipesAndValves, 2000);
    });

    // Override the updateSystemFromMqtt function to ensure pipes and valves remain visible
    const originalUpdateSystemFromMqtt = window.updateSystemFromMqtt;
    if (typeof originalUpdateSystemFromMqtt === 'function') {
      window.updateSystemFromMqtt = function(mqttData) {
        const result = originalUpdateSystemFromMqtt.apply(this, arguments);

        // Force branch pipes to be visible
        document.querySelectorAll('.branch').forEach(branch => {
          branch.style.display = 'block';
          branch.style.visibility = 'visible';
          branch.style.opacity = '1';
          branch.style.zIndex = '20';
        });

        // Force valves to be visible
        document.querySelectorAll('.valve').forEach(valve => {
          valve.style.display = 'block';
          valve.style.visibility = 'visible';
          valve.style.opacity = '1';
          valve.style.zIndex = '30';
        });

        return result;
      };
    }
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/mqtt/5.5.1/mqtt.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>


  </main>
  <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
  <script src="<?php echo BASE_PATH; ?>assets/js/project/dashboard1.js"></script>

  <?php
  include(BASE_PATH . "assets/html/body-end.php");
  include(BASE_PATH . "assets/html/html-end.php");
  ?>



<?php
require_once 'config-path.php';
require_once '../session/session-manager.php';
SessionManager::checkSession();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Dashboard</title>
    <!-- Bootstrap CSS -->
    <!-- Bootstrap JavaScript -->

    <style>
        /* Custom styles to complement Bootstrap */

        .chart-container {
            height: 150px;
            position: relative;
        }

        .updates-container {
            max-height: none;
            overflow-y: auto;
            height: 100%;
        }

        .inactive-updates-container {
            max-height: none;
            overflow-y: auto;
            height: 100%;
        }

        .alert-item {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Remove list-group-item conflicting styles */
            position: relative;
            display: block;
            color: inherit;
            text-decoration: none;
        }

        .device-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .device-name {
            color: #2563eb;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .timestamp {
            color: #6b7280;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .alert-message {
            color: #374151;
            font-size: 0.9rem;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .contact-info {
            display: flex;
            align-items: center;
            gap: 16px;
            /* border-top: 1px solid #f3f4f6; */
            padding-top: 8px;
            flex-wrap: wrap;
            /* Allow wrapping for small screens */
        }

        .electrician-info,
        .phone-number {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #059669;
            font-size: 0.85rem;
            text-decoration: none;
            white-space: nowrap;
            /* Prevent breaking in larger screens */
        }

        .phone-number:hover {
            text-decoration: underline;
        }

        .bi {
            font-size: 1rem;
        }

        @media (max-width: 576px) {
            .contact-info {
                flex-direction: column;
                /* Stack name and phone number */
                align-items: flex-start;
                gap: 4px;
            }

            .electrician-info {
                font-size: 0.8rem;
                /* Increase size for readability */
                font-weight: bold;
            }

            .phone-number {
                font-size: 0.9rem;
            }
        }

        .map-container {
            height: 400px;
            background-color: #f1f5f9;
            border-radius: 0.5rem;
            /* margin-bottom: 0.5rem; */
        }

        /* Updated styles for new layout */
        .top-cards-row {
            height: auto;
        }

        .map-and-updates-row {
            margin-top: 1rem;
        }

        /* Right panel styles */
        #updates-panel .row {
            height: 100%;
        }

        .inactive-updates-card {
            height: auto;
            min-height: 0;
        }

        .inactive-updates-card .card-body {
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .inactive-updates-card .inactive-updates-container {
            flex: 1;
            min-height: 0;
        }

        .updates-card {
            height: 100%;
            min-height: 0;
        }

        .updates-card .card-body {
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .updates-card .updates-container {
            flex: 1;
            min-height: 0;
        }

        /* Flex container for right panel */
        .updates-panel-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
    </style>
</head>

<body>
    <title>Device Dashboard</title>
    <?php
    include(BASE_PATH . "assets/html/start-page.php");
    ?>
    <div class="d-flex flex-column flex-shrink-0 p-3 main-content ">
        <div class="container-fluid">
            <div class="row d-flex align-items-center mb-2">
                <div class="col-12 p-0">
                    <p class="m-0 p-0"><span class="text-body-tertiary">Pages / </span><span>Device Dashboard</span></p>
                </div>
            </div>

            <?php include(BASE_PATH . "dropdown-selection/device-list.php"); ?>

            <div class="row mt-3">
                <!-- Left Section (Cards + Map) -->
                <div class="col-lg-8">
                    <div class="row pe-0 pe-lg-2">
                        <!-- Top Row: 3 Cards -->
                        <div class="col-12 rounded mt-2 p-0">
                            <div class="row g-3 top-cards-row">
                                <!-- Lights Card -->
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex align-items-center">
                                            <h6 class="card-title mb-0 fw-semibold">
                                                <i class="bi bi-brightness-high me-2"></i>Lights
                                            </h6>
                                        </div>

                                        <div class="card-body d-flex flex-column ">
                                            <div class="text-center mb-3">
                                                <h2 id="total-lights">1250</h2>
                                                <p class="text-muted mb-3">Total Lights Installed</p>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="p-2 bg-success bg-opacity-10 rounded">
                                                            <h4 id="lights-on-percentage" class="text-success-emphasis mb-0">78%</h4>
                                                            <small>On</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-2 bg-danger bg-opacity-10 rounded">
                                                            <h4 id="lights-off-percentage" class="text-danger mb-0">22%</h4>
                                                            <small>Off</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chart-container mt-auto">
                                                <canvas id="lights-chart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CCMS Devices Card -->
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex align-items-center">
                                            <h6 class="card-title mb-0 fw-semibold">
                                                <i class="bi bi-cpu me-2"></i>CCMS Devices
                                            </h6>
                                        </div>

                                        <div class="card-body d-flex flex-column pointer">
                                            <div class="text-center mb-3">
                                                <h2 id="total-ccms">45</h2>
                                                <p class="text-muted mb-3">Total CCMS Devices</p>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="p-2 bg-success bg-opacity-10 rounded cursor-pointer" onclick="activeModal()">
                                                            <h4 id="ccms-on" class="text-success-emphasis mb-0">38</h4>
                                                            <small>Active</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-2 bg-danger bg-opacity-10 rounded cursor-pointer" onclick="openNonActiveModal()">
                                                            <h4 id="ccms-off" class="text-danger mb-0">7</h4>
                                                            <small>InActive</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chart-container mt-auto">
                                                <canvas id="ccms-chart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Connected Load Card -->
                                <div class="col-sm-12 col-md-6 col-lg-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex align-items-center">
                                            <h6 class="card-title mb-0 fw-semibold">
                                                <i class="bi bi-lightning-charge-fill me-2"></i>Connected Load (kW)
                                            </h6>
                                        </div>

                                        <div class="card-body d-flex flex-column ">
                                            <div class="text-center mb-3">
                                                <h2 id="cumulative-load"></h2>
                                                <p class="text-muted mb-3">Installed Load</p>
                                                <div class="row g-2">
                                                    <!-- Active Load -->
                                                    <div class="col-12 col-md-6">
                                                        <div class="p-2 bg-primary bg-opacity-10 rounded d-flex align-items-center justify-content-center h-100 text-center">
                                                            <div>
                                                                <h4 id="installed-load" class="text-primary mb-0"></h4>
                                                                <small>Active Load</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Inactive Load -->
                                                    <div class="col-12 col-md-6" id="inactive-load-container">
                                                        <!-- Content injected by JS -->
                                                        <div class="p-2 bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center h-100 text-center">
                                                            <div>
                                                                <h4 id="active-load" class="text-secondary mb-0"></h4>
                                                                <small>Inactive Load</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="chart-container mt-auto">
                                                <canvas id="load-chart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Bottom Row: Map -->
                    <div class="row mt-3 ">
                        <div class="col-12 ps-0 pe-2">
                            <div class="card" id="map-card">
                                <div class="card-header">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
                                        <!-- Title -->
                                        <h6 class="card-title mb-2 mb-sm-0">
                                            <i class="bi bi-geo-alt-fill"></i> Device Map
                                        </h6>

                                        <!-- Controls container - same line on larger screens -->
                                        <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="refreshMap()">
                                                Refresh
                                            </button>
                                            <select class="form-select form-select-sm" id="locationsDropdown" style="min-width: 150px;"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">

                                    <div class="map-container" id="map"></div>
                                    <div class="col-12 mt-2">
                                        <small>* <i class="bi bi-geo-alt-fill text-danger"></i> Lights are Turned OFF</small>
                                        <small>* <i class="bi bi-geo-alt-fill text-success"></i> Lights are turned ON</small>
                                        <small>* <i class="bi bi-geo-alt-fill text-warning"></i> Poor Network Units</small>
                                        <small>* <i class="bi bi-geo-alt-fill text-purple"></i> Communication Loss Units</small>
                                        <small>* <i class="bi bi-geo-alt-fill text-primary"></i> Power Fail Units</small>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Right Section: Updates Panel -->
                <div class="col-lg-4" id="updates-panel">
                    <div class="row ps-0 ps-lg-2 h-100">
                        <div class="col-12 rounded mt-4 mt-lg-2 p-0 d-flex flex-column h-100">
                            <!-- Inactive Devices Updates Card - Top -->
                            <div class="card inactive-updates-card mb-3" id="inactive-updates-card">
                                <div class="card-header fw-bold">
                                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>InActive Devices
                                </div>
                                <div class="card-body">
                                    <div class="inactive-updates-container overflow-y-auto" id="inactive-updates-container"></div>
                                </div>
                            </div>

                            <!-- Regular Updates Card - Bottom, aligned with Map -->
                            <div class="card updates-card flex-fill" id="updates-card">
                                <div class="card-header fw-bold">
                                    <i class="bi bi-chat-dots-fill"></i> Updates
                                </div>
                                <div class="card-body">
                                    <div class="updates-container overflow-y-auto" id="updates-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Function to align cards heights properly
        function alignCardsHeights() {
            const topCards = document.querySelectorAll('.top-cards-row .card');
            const inactiveUpdatesCard = document.getElementById('inactive-updates-card');
            const mapCard = document.getElementById('map-card');
            const updatesCard = document.getElementById('updates-card');

            // Reset heights first
            if (inactiveUpdatesCard) inactiveUpdatesCard.style.height = 'auto';
            if (updatesCard) updatesCard.style.height = 'auto';

            // Get the height of the top cards row
            if (topCards.length > 0 && inactiveUpdatesCard) {
                const topCardsHeight = Math.max(...Array.from(topCards).map(card => card.offsetHeight));
                inactiveUpdatesCard.style.height = topCardsHeight + 'px';
            }

            // Align updates card with map card
            if (mapCard && updatesCard) {
                const mapCardHeight = mapCard.offsetHeight;
                updatesCard.style.height = mapCardHeight + 'px';
            }
        }

        // Call the function when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initial alignment
            setTimeout(alignCardsHeights, 100);

            // Re-align on window resize
            window.addEventListener('resize', alignCardsHeights);
        });

        // Re-align after map is loaded
        window.addEventListener('load', function() {
            setTimeout(alignCardsHeights, 500);
        });
    </script>

    <?php
    include(BASE_PATH . "dashboard/dashboard_modals.php");
    ?>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvlom5_AlCYoIgXu94yl_VyRRRBc0xSFQ&callback=initMap" async defer></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/sidebar-menu.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/map.js"></script>
    <script src="<?php echo BASE_PATH; ?>assets/js/project/device-dashboard.js"></script>

    <?php
    include(BASE_PATH . "assets/html/body-end.php");
    include(BASE_PATH . "assets/html/html-end.php");
    ?>
</body>

</html>